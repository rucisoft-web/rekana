<?php

namespace App\Services;

use App\Enums\BidStatus;
use App\Enums\CardStatus;
use App\Enums\ContractStatus;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Enums\PqStatus;
use App\Enums\RegistrationStatus;
use App\Enums\RiskLevel;
use App\Enums\TenderStatus;
use App\Models\AccessCard;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\ContractorRegistration;
use App\Models\CsmsCertificate;
use App\Models\InductionAttendee;
use App\Models\Owner;
use App\Models\Prequalification;
use App\Models\RiskLine;
use App\Models\SafetyStatistic;
use App\Models\TenderBid;
use App\Models\Worker;
use App\Models\WorkerCompetency;
use App\Models\WorkPermit;
use App\Models\WorkPlan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Data dasbor monitoring rekanan untuk satu owner. Semua angka dihitung langsung dari data operasional
 * (prakualifikasi, sertifikat, statistik K3, kontrak, kartu akses, izin kerja), tanpa tabel ringkasan.
 */
class ContractorDashboard
{
    public function __construct(private MobilizationService $mobilization) {}

    public function data(Owner $owner): array
    {
        $cfg = config('rekana.dashboard');
        $today = today();
        $window = $today->copy()->addDays($cfg['expiry_days']);

        $registrations = ContractorRegistration::with('contractor')->get();
        $contractorIds = $registrations->pluck('contractor_id');
        $contractors = Contractor::whereIn('id', $contractorIds)->with('safetyStatistics')->get()->keyBy('id');

        $pqs = Prequalification::with('contractor')->orderByDesc('id')->get();
        $latestPq = $pqs->unique('contractor_id')->keyBy('contractor_id');
        $certs = CsmsCertificate::with('contractor')->get();
        $validCerts = $certs->filter->isValid()->sortByDesc(fn ($c) => $c->max_risk->rank())->unique('contractor_id')->keyBy('contractor_id');

        $contracts = Contract::with(['contractor', 'workPlan.area'])->get();
        $liveContracts = $contracts->whereIn('status', [ContractStatus::Mobilisasi, ContractStatus::Berjalan]);
        $cards = AccessCard::with(['worker', 'contract.workPlan.area'])->get();
        $usableCards = $cards->filter->isUsable();

        $permits = WorkPermit::with('contract.contractor')->get();
        $now = now();
        $activePermits = $permits->where('status', PermitStatus::Aktif);
        $waitingPermits = $permits->where('status', PermitStatus::Diajukan);

        $blocked = $this->blockedWorkers($liveContracts, $usableCards);
        $docs = ContractorDocument::whereIn('contractor_id', $contractorIds)->whereNotNull('expires_at')->with('contractor')->get();
        $docsDue = $docs->filter(fn ($d) => $d->expires_at->lte($today->copy()->addDays(30)));

        $waitingPq = $pqs->whereIn('status', [PqStatus::Diajukan, PqStatus::Dinilai]);
        $oldestPqDays = $waitingPq->filter->submitted_at->map(fn ($p) => (int) $p->submitted_at->diffInDays($now))->max();
        $expiringCerts = $validCerts->filter(fn ($c) => $c->valid_until->lte($window));

        $safety = $contractors->map(fn (Contractor $c) => ['id' => $c->id, 'name' => $c->name] + $this->summary($c->safetyStatistics));

        return [
            'owner' => $owner,
            'today' => $today,
            'cfg' => $cfg,
            'thresholds' => $owner->pqThresholds(),
            'kpi' => [
                'registered' => $registrations->where('status', RegistrationStatus::Aktif)->count(),
                'registrations' => $registrations->count(),
                'blacklisted' => $registrations->where('status', RegistrationStatus::DaftarHitam)->count(),
                'pending_registration' => $registrations->where('status', RegistrationStatus::Menunggu)->count(),
                'certs_valid' => $validCerts->count(),
                'certs_expiring' => $expiringCerts->count(),
                'pq_waiting' => $waitingPq->count(),
                'pq_oldest_days' => $oldestPqDays,
                'pq_clarification' => $pqs->where('status', PqStatus::Klarifikasi)->count(),
                'contracts_live' => $liveContracts->count(),
                'contract_value' => (float) $liveContracts->sum('value'),
                'workers_carded' => $usableCards->unique('worker_id')->count(),
                'workers_blocked' => $blocked->count(),
                'permits_active' => $activePermits->count(),
                'permits_waiting' => $waitingPermits->count(),
                'docs_due' => $docsDue->count(),
                'docs_expired' => $docsDue->filter(fn ($d) => $d->expires_at->lt($today))->count(),
            ],
            'funnel' => $this->funnel($registrations, $pqs, $contracts),
            'pq_status' => collect(PqStatus::cases())->map(fn ($s) => ['label' => $s->getLabel(), 'color' => $s->getColor(), 'value' => $pqs->where('status', $s)->count()])->filter(fn ($r) => $r['value'] > 0)->values()->all(),
            'pq_scores' => $latestPq->filter(fn ($p) => $p->score !== null)->sortByDesc('score')->map(fn ($p) => [
                'name' => $this->short($p->contractor->name), 'score' => (float) $p->score,
                'result' => $p->critical_failed ? 'gagal_kritis' : ($p->result_risk?->value ?? 'tidak_lulus'),
            ])->values()->all(),
            'matrix' => $this->matrix($safety, $latestPq, $cfg),
            'safety_trend' => $this->safetyTrend($contractors),
            'permit_trend' => $this->permitTrend($permits, $cfg['weeks']),
            'permit_by_contractor' => $permits->groupBy(fn ($p) => $p->contract->contractor->name)->map(fn ($g, $name) => [
                'name' => $this->short($name),
                'closed' => $g->where('status', PermitStatus::Ditutup)->count(),
                'cancelled' => $g->where('status', PermitStatus::Dibatalkan)->count(),
                'open' => $g->whereIn('status', [PermitStatus::Draf, PermitStatus::Diajukan, PermitStatus::Disetujui, PermitStatus::Aktif])->count(),
            ])->sortByDesc(fn ($r) => $r['closed'] + $r['cancelled'] + $r['open'])->values()->all(),
            'expiry' => $this->expiry($validCerts, $docs, $liveContracts, $usableCards),
            'risk_mix' => collect(RiskLevel::cases())->map(fn ($r) => ['label' => $r->getLabel(), 'color' => $r->getColor(), 'value' => WorkPlan::where('risk_category', $r)->count()])->all(),
            'hazards' => RiskLine::selectRaw('hazard, count(*) as n, avg(score) as avg_score, max(score) as max_score')->groupBy('hazard')->orderByDesc('n')->limit(8)->get()
                ->map(fn ($r) => ['label' => $r->hazard->getLabel(), 'n' => (int) $r->n, 'avg' => round((float) $r->avg_score, 1), 'max' => (int) $r->max_score])->all(),
            'area_load' => $this->areaLoad($usableCards, $permits),
            'induction' => [
                'passed' => InductionAttendee::where('passed', true)->count(),
                'failed' => InductionAttendee::where('passed', false)->count(),
            ],
            'scorecard' => $this->scorecard($registrations, $latestPq, $validCerts, $safety, $liveContracts, $usableCards, $permits, $cfg, $window),
            'decisions' => $this->decisions($waitingPq, $oldestPqDays, $pqs, $waitingPermits, $expiringCerts, $docsDue, $blocked, $safety, $cfg),
            'blocked' => $blocked->take(8)->values()->all(),
        ];
    }

    /** @return array{manhours: int, ltifr: ?float, trir: ?float, fatalities: int} */
    private function summary(Collection $stats, int $years = 3): array
    {
        $rows = $stats->sortByDesc('year')->take($years);
        $hours = (int) $rows->sum('manhours');

        return [
            'manhours' => $hours,
            'ltifr' => $hours > 0 ? round($rows->sum('lti_cases') * 1_000_000 / $hours, 2) : null,
            'trir' => $hours > 0 ? round($rows->sum('recordable_cases') * 200_000 / $hours, 2) : null,
            'fatalities' => (int) $rows->sum('fatalities'),
        ];
    }

    private function short(string $name): string
    {
        return trim(preg_replace('/^(PT|CV)\.?\s+/i', '', $name));
    }

    /** Pekerja rekanan dengan kontrak berjalan yang belum memegang kartu berlaku, beserta alasannya. */
    private function blockedWorkers(Collection $liveContracts, Collection $usableCards): Collection
    {
        $carded = $usableCards->map(fn ($c) => $c->contract_id.'-'.$c->worker_id)->flip();

        return $liveContracts->flatMap(function (Contract $contract) use ($carded) {
            return Worker::where('contractor_id', $contract->contractor_id)->where('is_active', true)->get()
                ->reject(fn (Worker $w) => $carded->has($contract->id.'-'.$w->id))
                ->map(fn (Worker $w) => [
                    'worker' => $w->name, 'position' => $w->position, 'contractor' => $this->short($contract->contractor->name),
                    'reasons' => $this->mobilization->cardBlockers($contract, $w) ?: ['Kartu belum diterbitkan.'],
                ]);
        })->values();
    }

    /** Corong enam tahap IOGP 423 dalam angka rekanan. */
    private function funnel(Collection $registrations, Collection $pqs, Collection $contracts): array
    {
        $invited = TenderBid::distinct()->count('contractor_id');

        return [
            ['label' => 'Terdaftar', 'value' => $registrations->count()],
            ['label' => 'PQ diajukan', 'value' => $pqs->whereNotNull('submitted_at')->unique('contractor_id')->count()],
            ['label' => 'PQ lulus (bersertifikat)', 'value' => $pqs->where('status', PqStatus::Disetujui)->filter(fn ($p) => ! $p->critical_failed && $p->result_risk)->unique('contractor_id')->count()],
            ['label' => 'Diundang seleksi', 'value' => $invited],
            ['label' => 'Terkontrak', 'value' => $contracts->unique('contractor_id')->count()],
            ['label' => 'Izin mulai kerja', 'value' => $contracts->whereNotNull('start_approved_at')->unique('contractor_id')->count()],
        ];
    }

    /** Matriks keputusan: skor PQ (sumbu Y) terhadap LTIFR 3 tahun (sumbu X), ukuran gelembung = jam kerja. */
    private function matrix(Collection $safety, Collection $latestPq, array $cfg): array
    {
        $pass = config('rekana.pq.thresholds.menengah');

        return $safety->map(function ($s) use ($latestPq, $cfg, $pass) {
            $pq = $latestPq->get($s['id']);
            if (! $pq || $pq->score === null || $s['ltifr'] === null) {
                return null;
            }
            $good = (float) $pq->score >= $pass;
            $safe = $s['ltifr'] <= $cfg['ltifr_alert'] && $s['fatalities'] === 0;

            return [
                // Sumbu X dibatasi agar rekanan yang sangat buruk tidak memampatkan yang lain; nilai asli ada di 'ltifr'.
                'name' => $this->short($s['name']), 'x' => min($s['ltifr'], $cfg['ltifr_alert'] * 6), 'ltifr' => $s['ltifr'], 'y' => (float) $pq->score,
                'r' => max(6, min(26, round(sqrt($s['manhours']) / 30))), 'manhours' => $s['manhours'],
                'quadrant' => match (true) {
                    $good && $safe => 'andalan', $good => 'pantau', $safe => 'bina', default => 'hindari',
                },
            ];
        })->filter()->values()->all();
    }

    private function safetyTrend(Collection $contractors): array
    {
        $rows = SafetyStatistic::whereIn('contractor_id', $contractors->keys())->get()->groupBy('year')->sortKeys();

        return [
            'labels' => $rows->keys()->map(fn ($y) => (string) $y)->values()->all(),
            'manhours' => $rows->map(fn ($g) => (int) $g->sum('manhours'))->values()->all(),
            'ltifr' => $rows->map(fn ($g) => $g->sum('manhours') ? round($g->sum('lti_cases') * 1_000_000 / $g->sum('manhours'), 2) : 0)->values()->all(),
            'trir' => $rows->map(fn ($g) => $g->sum('manhours') ? round($g->sum('recordable_cases') * 200_000 / $g->sum('manhours'), 2) : 0)->values()->all(),
            'fatalities' => $rows->map(fn ($g) => (int) $g->sum('fatalities'))->values()->all(),
        ];
    }

    private function permitTrend(Collection $permits, int $weeks): array
    {
        $start = now()->startOfWeek()->subWeeks($weeks - 1);
        $labels = [];
        $byType = [];
        $cancelRate = [];

        for ($i = 0; $i < $weeks; $i++) {
            $from = $start->copy()->addWeeks($i);
            $to = $from->copy()->endOfWeek();
            $labels[] = $from->translatedFormat('d M');
            $week = $permits->filter(fn ($p) => $p->starts_at->between($from, $to));
            foreach (PermitType::cases() as $type) {
                $byType[$type->value][] = $week->where('type', $type)->count();
            }
            $cancelRate[] = $week->count() ? round($week->where('status', PermitStatus::Dibatalkan)->count() * 100 / $week->count(), 1) : 0;
        }

        $colors = ['kerja_panas' => '#e11d48', 'ruang_terbatas' => '#7c3aed', 'ketinggian' => '#f59e0b', 'loto' => '#0ea5e9', 'umum' => '#94a3b8'];

        return [
            'labels' => $labels,
            'series' => collect(PermitType::cases())->map(fn ($t) => ['label' => $t->getLabel(), 'data' => $byType[$t->value], 'color' => $colors[$t->value]])
                ->filter(fn ($s) => array_sum($s['data']) > 0)->values()->all(),
            'cancel_rate' => $cancelRate,
        ];
    }

    /** Hal yang akan kedaluwarsa per bulan (6 bulan ke depan); kolom pertama berisi yang sudah lewat. */
    private function expiry(Collection $validCerts, Collection $docs, Collection $liveContracts, Collection $usableCards): array
    {
        $months = collect(range(0, 5))->map(fn ($i) => today()->startOfMonth()->addMonths($i));
        $labels = array_merge(['Sudah lewat'], $months->map(fn ($m) => $m->translatedFormat('M Y'))->all());
        $workerIds = $usableCards->pluck('worker_id')->merge(Worker::whereIn('contractor_id', $liveContracts->pluck('contractor_id'))->pluck('id'))->unique();
        $workers = Worker::whereIn('id', $workerIds)->get();
        $comps = WorkerCompetency::whereIn('worker_id', $workerIds)->whereNotNull('expires_at')->get();

        $bucket = function (Collection $dates) use ($months) {
            $out = [$dates->filter(fn ($d) => $d->lt(today()))->count()];
            foreach ($months as $m) {
                $out[] = $dates->filter(fn ($d) => $d->gte(today()) && $d->between($m, $m->copy()->endOfMonth()))->count();
            }

            return $out;
        };

        return [
            'labels' => $labels,
            'series' => [
                ['label' => 'Sertifikat CSMS', 'data' => $bucket($validCerts->pluck('valid_until')), 'color' => '#1d4ed8'],
                ['label' => 'Dokumen rekanan', 'data' => $bucket($docs->pluck('expires_at')), 'color' => '#f59e0b'],
                ['label' => 'Surat sehat pekerja', 'data' => $bucket($workers->pluck('medical_until')->filter()), 'color' => '#e11d48'],
                ['label' => 'Kompetensi pekerja', 'data' => $bucket($comps->pluck('expires_at')), 'color' => '#7c3aed'],
                ['label' => 'Kartu akses', 'data' => $bucket($usableCards->pluck('valid_until')), 'color' => '#10b981'],
            ],
        ];
    }

    private function areaLoad(Collection $usableCards, Collection $permits): array
    {
        $since = now()->subDays(30);
        $areas = \App\Models\Area::orderBy('code')->get();

        return $areas->map(fn ($a) => [
            'label' => $a->name,
            'workers' => $usableCards->filter(fn ($c) => $c->contract->workPlan?->area_id === $a->id)->unique('worker_id')->count(),
            'permits' => $permits->filter(fn ($p) => $p->area_id === $a->id && $p->starts_at->gte($since))->count(),
            'high' => $permits->filter(fn ($p) => $p->area_id === $a->id && $p->starts_at->gte($since) && in_array($p->type, [PermitType::KerjaPanas, PermitType::RuangTerbatas, PermitType::Ketinggian], true))->count(),
        ])->filter(fn ($r) => $r['workers'] + $r['permits'] > 0)->sortByDesc('permits')->values()->all();
    }

    private function scorecard(Collection $registrations, Collection $latestPq, Collection $validCerts, Collection $safety, Collection $liveContracts, Collection $usableCards, Collection $permits, array $cfg, Carbon $window): array
    {
        $since = now()->subDays(90);

        return $registrations->map(function (ContractorRegistration $reg) use ($latestPq, $validCerts, $safety, $liveContracts, $usableCards, $permits, $cfg, $window, $since) {
            $id = $reg->contractor_id;
            $pq = $latestPq->get($id);
            $cert = $validCerts->get($id);
            $s = $safety->get($id) ?? ['ltifr' => null, 'trir' => null, 'fatalities' => 0, 'manhours' => 0];
            $p = $permits->filter(fn ($x) => $x->contract->contractor_id === $id && $x->starts_at->gte($since));
            $cancelPct = $p->count() ? round($p->where('status', PermitStatus::Dibatalkan)->count() * 100 / $p->count()) : null;

            [$advice, $tone] = match (true) {
                $reg->status === RegistrationStatus::DaftarHitam => ['Daftar hitam', 'danger'],
                $s['fatalities'] > 0 || ($s['ltifr'] ?? 0) > 2 * $cfg['ltifr_alert'] => ['Tinjau ulang', 'danger'],
                in_array($pq?->status, [PqStatus::Diajukan, PqStatus::Dinilai], true) => ['Putuskan PQ', 'warning'],
                $pq?->status === PqStatus::Klarifikasi => ['Tunggu klarifikasi', 'warning'],
                $cert && $cert->valid_until->lte($window) => ['Perpanjang PQ', 'warning'],
                ! $cert => ['Belum bersertifikat', 'gray'],
                ($s['ltifr'] ?? 0) > $cfg['ltifr_alert'] || ($s['trir'] ?? 0) > $cfg['trir_alert'] => ['Pantau ketat', 'warning'],
                default => ['Andalan', 'success'],
            };

            return [
                'name' => $reg->contractor->name, 'field' => $reg->contractor->business_field, 'vendor' => $reg->vendor_code,
                'registration' => $reg->status, 'pq_status' => $pq?->status, 'pq_score' => $pq?->score !== null ? (float) $pq->score : null,
                'cert_risk' => $cert?->max_risk, 'cert_until' => $cert?->valid_until,
                'ltifr' => $s['ltifr'], 'trir' => $s['trir'], 'fatalities' => $s['fatalities'],
                'contracts' => $liveContracts->where('contractor_id', $id)->count(),
                'workers' => $usableCards->filter(fn ($c) => $c->contract->contractor_id === $id)->unique('worker_id')->count(),
                'permits' => $p->count(), 'cancel_pct' => $cancelPct,
                'advice' => $advice, 'tone' => $tone,
            ];
        })->sortBy(fn ($r) => ['danger' => 0, 'warning' => 1, 'gray' => 2, 'success' => 3][$r['tone']])->values()->all();
    }

    private function decisions(Collection $waitingPq, ?int $oldestPqDays, Collection $pqs, Collection $waitingPermits, Collection $expiringCerts, Collection $docsDue, Collection $blocked, Collection $safety, array $cfg): array
    {
        $out = [];
        if ($waitingPermits->count()) {
            $out[] = ['danger', $waitingPermits->count().' izin kerja menunggu persetujuan', 'Pekerjaan berisiko tinggi tidak boleh dimulai sebelum izin disetujui dan diaktifkan.', 'izin'];
        }
        if ($waitingPq->count()) {
            $out[] = ['warning', $waitingPq->count().' prakualifikasi menunggu keputusan', 'Yang tertua sudah '.($oldestPqDays ?? 0).' hari. Rekanan tidak dapat diundang seleksi sebelum PQ diputuskan.', 'pq'];
        }
        if ($blocked->count()) {
            $out[] = ['danger', $blocked->count().' pekerja tertahan di gerbang', 'Pekerja kontrak berjalan tanpa kartu berlaku: surat sehat kedaluwarsa atau induksi belum lulus.', 'kartu'];
        }
        if ($expiringCerts->count()) {
            $names = $expiringCerts->map(fn ($c) => $this->short($c->contractor->name))->implode(', ');
            $out[] = ['warning', $expiringCerts->count().' sertifikat CSMS habis dalam '.$cfg['expiry_days'].' hari', $names.'. Mulai PQ ulang agar rekanan tetap bisa ditugaskan.', 'sertifikat'];
        }
        if ($docsDue->count()) {
            $out[] = ['warning', $docsDue->count().' dokumen rekanan kedaluwarsa atau habis dalam 30 hari', $docsDue->take(3)->map(fn ($d) => $d->title.' ('.$this->short($d->contractor->name).')')->implode('; ').'.', 'dokumen'];
        }
        $risky = $safety->filter(fn ($s) => $s['fatalities'] > 0 || ($s['ltifr'] ?? 0) > $cfg['ltifr_alert']);
        if ($risky->count()) {
            $out[] = ['danger', $risky->count().' rekanan di atas ambang LTIFR '.$cfg['ltifr_alert'], $risky->map(fn ($s) => $this->short($s['name']).' ('.$s['ltifr'].')')->implode(', ').'. Pertimbangkan audit lapangan atau pengawasan tambahan.', 'statistik'];
        }
        if ($n = $pqs->where('status', PqStatus::Klarifikasi)->count()) {
            $out[] = ['info', $n.' PQ menunggu klarifikasi rekanan', 'Ingatkan rekanan melengkapi bukti yang diminta.', 'pq'];
        }
        $openTenders = \App\Models\Tender::where('status', TenderStatus::Dibuka)->withCount(['bids as waiting' => fn ($q) => $q->where('status', BidStatus::Diajukan)])->get();
        if ($openTenders->sum('waiting')) {
            $out[] = ['info', $openTenders->sum('waiting').' HSE Plan menunggu dinilai', 'Seleksi '.$openTenders->pluck('title')->implode(', ').'.', 'seleksi'];
        }

        return $out;
    }
}
