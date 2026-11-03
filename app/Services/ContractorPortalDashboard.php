<?php

namespace App\Services;

use App\Enums\BidStatus;
use App\Enums\ContractStatus;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Enums\PqStatus;
use App\Models\AccessCard;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\CsmsCertificate;
use App\Models\InductionAttendee;
use App\Models\Prequalification;
use App\Models\TenderBid;
use App\Models\Worker;
use App\Models\WorkerCompetency;
use App\Models\WorkPermit;
use Illuminate\Support\Collection;

/**
 * Dasbor portal kontraktor: pekerja, kontrak, izin kerja, status PQ, dan hal yang harus diurus,
 * dihimpun dari seluruh owner tempat rekanan ini terdaftar.
 */
class ContractorPortalDashboard
{
    public function __construct(private MobilizationService $mobilization) {}

    public function data(Contractor $contractor): array
    {
        $cfg = config('rekana.dashboard');
        $today = today();

        $registrations = $contractor->registrations()->with('owner')->get();
        $pqs = Prequalification::withoutGlobalScopes()->where('contractor_id', $contractor->id)->with('owner')->orderByDesc('id')->get()->unique('owner_id');
        $certs = CsmsCertificate::withoutGlobalScopes()->where('contractor_id', $contractor->id)->with('owner')->get()->filter->isValid()->unique('owner_id')->keyBy('owner_id');

        $contracts = Contract::withoutGlobalScopes()->where('contractor_id', $contractor->id)->with(['owner', 'workPlan.area'])->get();
        $live = $contracts->whereIn('status', [ContractStatus::Mobilisasi, ContractStatus::Berjalan]);
        $workers = Worker::where('contractor_id', $contractor->id)->where('is_active', true)->with('competencies')->orderBy('name')->get();
        $cards = AccessCard::withoutGlobalScopes()->whereIn('worker_id', $workers->pluck('id'))->with('contract.owner')->get();
        $usable = $cards->filter->isUsable();
        $inductions = InductionAttendee::withoutGlobalScopes()->whereIn('worker_id', $workers->pluck('id'))->with('induction')->get();
        $permits = WorkPermit::withoutGlobalScopes()->whereIn('contract_id', $contracts->pluck('id'))->with(['contract.owner', 'workers'])->get();

        $roster = $workers->map(function (Worker $w) use ($usable, $live, $inductions, $today) {
            $card = $usable->where('worker_id', $w->id)->sortByDesc('valid_until')->first();
            $induction = $inductions->where('worker_id', $w->id)->sortByDesc('valid_until')->first();
            $reasons = $card ? [] : $live->flatMap(fn (Contract $c) => $this->mobilization->cardBlockers($c, $w))->unique()->values()->all();
            $soon = $w->competencies->filter(fn ($c) => $c->expires_at && $c->expires_at->lte($today->copy()->addDays(60)));

            return [
                'name' => $w->name, 'position' => $w->position,
                'card' => $card, 'medical' => $w->medical_until, 'medical_ok' => $w->medicalValid(),
                'induction' => $induction, 'competencies' => $w->competencies->pluck('name')->all(), 'comp_soon' => $soon->count(),
                'status' => $card ? 'siap' : ($live->isEmpty() ? 'cadangan' : 'tertahan'),
                'reasons' => $reasons ?: ($card || $live->isEmpty() ? [] : ['Kartu belum diterbitkan.']),
            ];
        });

        $docs = $contractor->contractorDocuments()->whereNotNull('expires_at')->get();
        $stats = $contractor->safetyStatistics()->orderBy('year')->get();

        return [
            'contractor' => $contractor,
            'today' => $today,
            'cfg' => $cfg,
            'kpi' => [
                'workers' => $workers->count(),
                'ready' => $roster->where('status', 'siap')->count(),
                'blocked' => $roster->where('status', 'tertahan')->count(),
                'permits_active' => $permits->where('status', PermitStatus::Aktif)->count(),
                'permits_waiting' => $permits->whereIn('status', [PermitStatus::Diajukan, PermitStatus::Disetujui])->count(),
                'contracts_live' => $live->count(),
                'owners' => $registrations->count(),
                'certs' => $certs->count(),
                'docs_due' => $docs->filter(fn ($d) => $d->expires_at->lte($today->copy()->addDays(30)))->count(),
            ] + $contractor->safetySummary(),
            'owners' => $registrations->map(fn ($r) => [
                'owner' => $r->owner->name, 'status' => $r->status, 'vendor' => $r->vendor_code,
                'pq' => $pqs->firstWhere('owner_id', $r->owner_id), 'cert' => $certs->get($r->owner_id),
                'thresholds' => $r->owner->pqThresholds(),
            ])->values()->all(),
            'contracts' => $live->map(function (Contract $c) use ($usable, $permits, $today) {
                $total = max(1, $c->start_date->diffInDays($c->end_date));
                $done = min($total, max(0, $c->start_date->diffInDays($today, false)));

                return [
                    'owner' => $c->owner->name, 'title' => $c->title, 'number' => $c->number, 'area' => $c->workPlan?->area?->name,
                    'risk' => $c->workPlan?->risk_category, 'start' => $c->start_date, 'end' => $c->end_date,
                    'progress' => round($done * 100 / $total), 'days_left' => (int) max(0, $today->diffInDays($c->end_date, false)),
                    'workers' => $usable->where('contract_id', $c->id)->unique('worker_id')->count(),
                    'permits' => $permits->where('contract_id', $c->id)->count(),
                    'active' => $permits->where('contract_id', $c->id)->where('status', PermitStatus::Aktif)->count(),
                ];
            })->values()->all(),
            'roster' => $roster->sortBy(fn ($r) => ['tertahan' => 0, 'siap' => 1, 'cadangan' => 2][$r['status']])->values()->all(),
            'worker_status' => [
                ['label' => 'Siap masuk (kartu aktif)', 'value' => $roster->where('status', 'siap')->count(), 'color' => '#10b981'],
                ['label' => 'Tertahan', 'value' => $roster->where('status', 'tertahan')->count(), 'color' => '#e11d48'],
                ['label' => 'Cadangan (tanpa kontrak)', 'value' => $roster->where('status', 'cadangan')->count(), 'color' => '#94a3b8'],
            ],
            'permit_trend' => $this->permitTrend($permits, $cfg['weeks']),
            'permit_types' => collect(PermitType::cases())->map(fn ($t) => ['label' => $t->getLabel(), 'value' => $permits->where('type', $t)->count()])->filter(fn ($r) => $r['value'])->values()->all(),
            'recent_permits' => $permits->sortByDesc('starts_at')->take(8)->values()->all(),
            'safety' => [
                'labels' => $stats->pluck('year')->map(fn ($y) => (string) $y)->all(),
                'ltifr' => $stats->map->ltifr()->all(),
                'trir' => $stats->map->trir()->all(),
                'manhours' => $stats->pluck('manhours')->map(fn ($h) => round($h / 1000))->all(),
            ],
            'expiry' => $this->expiry($workers, $usable, $docs),
            'todo' => $this->todo($contractor, $roster, $pqs, $certs, $docs, $permits, $cfg),
        ];
    }

    private function permitTrend(Collection $permits, int $weeks): array
    {
        $start = now()->startOfWeek()->subWeeks($weeks - 1);
        $out = ['labels' => [], 'closed' => [], 'cancelled' => [], 'open' => []];
        for ($i = 0; $i < $weeks; $i++) {
            $from = $start->copy()->addWeeks($i);
            $week = $permits->filter(fn ($p) => $p->starts_at->between($from, $from->copy()->endOfWeek()));
            $out['labels'][] = $from->translatedFormat('d M');
            $out['closed'][] = $week->where('status', PermitStatus::Ditutup)->count();
            $out['cancelled'][] = $week->where('status', PermitStatus::Dibatalkan)->count();
            $out['open'][] = $week->whereNotIn('status', [PermitStatus::Ditutup, PermitStatus::Dibatalkan])->count();
        }

        return $out;
    }

    private function expiry(Collection $workers, Collection $usable, Collection $docs): array
    {
        $months = collect(range(0, 5))->map(fn ($i) => today()->startOfMonth()->addMonths($i));
        $comps = WorkerCompetency::whereIn('worker_id', $workers->pluck('id'))->whereNotNull('expires_at')->pluck('expires_at');
        $bucket = function (Collection $dates) use ($months) {
            $out = [$dates->filter(fn ($d) => $d->lt(today()))->count()];
            foreach ($months as $m) {
                $out[] = $dates->filter(fn ($d) => $d->gte(today()) && $d->between($m, $m->copy()->endOfMonth()))->count();
            }

            return $out;
        };

        return [
            'labels' => array_merge(['Sudah lewat'], $months->map(fn ($m) => $m->translatedFormat('M Y'))->all()),
            'series' => [
                ['label' => 'Surat sehat', 'data' => $bucket($workers->pluck('medical_until')->filter()), 'color' => '#e11d48'],
                ['label' => 'Kompetensi / SIO', 'data' => $bucket($comps), 'color' => '#7c3aed'],
                ['label' => 'Kartu akses', 'data' => $bucket($usable->pluck('valid_until')), 'color' => '#10b981'],
                ['label' => 'Dokumen perusahaan', 'data' => $bucket($docs->pluck('expires_at')), 'color' => '#f59e0b'],
            ],
        ];
    }

    private function todo(Contractor $contractor, Collection $roster, Collection $pqs, Collection $certs, Collection $docs, Collection $permits, array $cfg): array
    {
        $out = [];
        foreach ($pqs->where('status', PqStatus::Klarifikasi) as $pq) {
            $out[] = ['danger', 'Klarifikasi PQ diminta '.$pq->owner->name, $pq->clarification_request];
        }
        foreach ($pqs->where('status', PqStatus::Draf) as $pq) {
            $out[] = ['warning', 'Kuesioner PQ '.$pq->owner->name.' belum diajukan', 'Lengkapi jawaban dan bukti, lalu ajukan.'];
        }
        $bids = TenderBid::withoutGlobalScopes()->where('contractor_id', $contractor->id)->where('status', BidStatus::Diundang)->with('tender.owner')->get();
        foreach ($bids as $bid) {
            $out[] = ['warning', 'Undangan seleksi: '.$bid->tender->title, 'Unggah HSE Plan untuk '.$bid->tender->owner->name.($bid->tender->closes_at ? ' sebelum '.$bid->tender->closes_at->translatedFormat('d M Y') : '').'.'];
        }
        foreach ($roster->where('status', 'tertahan') as $r) {
            $out[] = ['danger', $r['name'].' tertahan di gerbang', implode(' ', $r['reasons'])];
        }
        foreach ($roster->filter(fn ($r) => $r['medical'] && $r['medical_ok'] && $r['medical']->lte(today()->addDays(30))) as $r) {
            $out[] = ['warning', 'Surat sehat '.$r['name'].' habis '.$r['medical']->translatedFormat('d M Y'), 'Jadwalkan pemeriksaan kesehatan agar kartu tidak terblokir.'];
        }
        foreach ($docs->filter(fn ($d) => $d->expires_at->lte(today()->addDays(30))) as $d) {
            $out[] = [$d->expires_at->isPast() ? 'danger' : 'warning', $d->title.($d->expires_at->isPast() ? ' sudah kedaluwarsa' : ' habis '.$d->expires_at->translatedFormat('d M Y')), 'Unggah dokumen terbaru.'];
        }
        foreach ($certs->filter(fn ($c) => $c->valid_until->lte(today()->addDays($cfg['expiry_days']))) as $c) {
            $out[] = ['warning', 'Sertifikat CSMS '.$c->owner->name.' habis '.$c->valid_until->translatedFormat('d M Y'), 'Ajukan PQ ulang sebelum habis.'];
        }
        if ($n = $permits->where('status', PermitStatus::Draf)->count()) {
            $out[] = ['info', $n.' izin kerja masih draf', 'Lengkapi daftar periksa bersama pemilik pekerjaan.'];
        }

        return $out;
    }
}
