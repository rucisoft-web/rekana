<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Enums\RegistrationStatus;
use App\Enums\RiskLevel;
use App\Enums\Role;
use App\Models\Area;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\ContractorRegistration;
use App\Models\Induction;
use App\Models\InductionAttendee;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\PqAnswer;
use App\Models\QuestionnaireTemplate;
use App\Models\RiskLine;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkPermit;
use App\Models\WorkPlan;
use App\Services\MobilizationService;
use App\Services\PermitService;
use App\Services\PrequalificationService;
use App\Services\RiskAssessor;
use App\Services\TenderService;
use App\Support\CurrentOwner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Mesin data contoh. Setiap skenario (subkelas) hanya berisi data; alurnya dijalankan lewat layanan yang sama
 * dengan aplikasi (penilaian risiko, PQ, seleksi, mobilisasi, izin kerja), sehingga data contoh selalu
 * mematuhi aturan bisnis.
 *
 * Kunci staf baku: admin, pimpinan, pemilik, pengadaan, evaluator, pengawas, auditor.
 */
abstract class DemoScenario extends Seeder
{
    protected Owner $owner;

    protected QuestionnaireTemplate $template;

    /** @var array<string, User> */
    protected array $users = [];

    /** @var array<string, Area> */
    protected array $areas = [];

    /** @var array<string, Contractor> */
    protected array $contractors = [];

    /** @var array<string, Contract> */
    protected array $contracts = [];

    abstract public static function slug(): string;

    abstract protected function domain(): string;

    abstract protected function templateCode(): string;

    abstract protected function ownerData(): array;

    /** @return array<int, array{0: string, 1: string, 2: \App\Enums\AreaClass, 3: string}> */
    abstract protected function areaData(): array;

    /** @return array<string, array{0: string, 1: string}> kunci staf => [nama, jabatan] */
    abstract protected function staffData(): array;

    /** @return array<string, array> kunci => [nama, bidang, kota, direktur, statistik per tahun [jam, fatal, lti, tercatat]] */
    abstract protected function contractorData(): array;

    /** @return array<string, array<int, array{0: string, 1: string, 2: array<int, string>}>> */
    abstract protected function workerData(): array;

    /** @return array<string, array{0: RiskLevel, 1: int, 2: ?string, 3: string}> kunci => [target, skor dasar, butir kritis 0, tahap akhir] */
    abstract protected function pqData(): array;

    /** @return array<string, array> lihat seedWork() */
    abstract protected function planData(): array;

    /** @return array<int, array> [rencana, jenis, judul, nama pekerja, mulai, jam, uji gas, titik isolasi, status akhir] */
    abstract protected function permitData(): array;

    /** Dokumen tambahan per rekanan: kunci => [[jenis, judul, nomor, kedaluwarsa]]. */
    protected function extraDocuments(): array
    {
        return [];
    }

    protected function inductionTopics(): array
    {
        return ['k3_umum', 'bahaya_area', 'tanggap_darurat', 'apd', 'izin_kerja'];
    }

    public function run(): void
    {
        if (Owner::where('slug', static::slug())->exists()) {
            $this->command?->warn('Data contoh '.static::slug().' sudah ada.');

            return;
        }

        $this->call(TemplateSeeder::class);
        AuditLog::$enabled = false;

        $this->owner = Owner::create($this->ownerData() + ['slug' => static::slug(), 'is_demo' => true]);

        app(CurrentOwner::class)->runAs($this->owner, function () {
            $this->template = QuestionnaireTemplate::whereNull('owner_id')->where('code', $this->templateCode())->firstOrFail();
            $this->seedAreasAndUsers();
            $this->seedContractors();
            $this->seedPrequalifications();
            $this->seedWork();
            $this->seedPermits();
        });

        AuditLog::$enabled = true;
        $this->command?->info('Data contoh '.$this->owner->name.' dimuat. Sandi semua akun: '.config('rekana.demo.password'));
    }

    protected function user(string $local, string $name, ?string $domain = null): User
    {
        return User::updateOrCreate(['email' => $local.'@'.($domain ?? $this->domain())], ['name' => $name, 'password' => config('rekana.demo.password')]);
    }

    protected function seedAreasAndUsers(): void
    {
        foreach ($this->areaData() as [$code, $name, $class, $desc]) {
            $this->areas[$code] = Area::create(['code' => $code, 'name' => $name, 'hazard_class' => $class, 'description' => $desc]);
        }

        $roles = [
            'admin' => Role::Admin, 'pimpinan' => Role::Pimpinan, 'pemilik' => Role::PemilikPekerjaan, 'pengadaan' => Role::Pengadaan,
            'evaluator' => Role::Evaluator, 'pengawas' => Role::Pengawas, 'auditor' => Role::Pengamat,
        ];

        foreach ($this->staffData() as $key => [$name, $title]) {
            $this->users[$key] = $this->user($key, $name);
            Membership::create(['user_id' => $this->users[$key]->id, 'role' => $roles[$key], 'position_title' => $title]);
        }

        $guest = User::firstOrCreate(['email' => User::DEMO_GUEST_EMAIL], ['name' => 'Tamu Demo', 'password' => str()->password(24)]);
        Membership::create(['user_id' => $guest->id, 'role' => Role::Pengamat, 'position_title' => 'Tamu (hanya lihat)']);
    }

    protected function seedContractors(): void
    {
        $n = 0;
        foreach ($this->contractorData() as $key => [$name, $field, $city, $director, $stats]) {
            $n++;
            $domain = $key.'.'.$this->domain();
            $c = Contractor::create([
                'name' => $name, 'business_field' => $field, 'city' => $city, 'director_name' => $director,
                'hse_contact' => 'HSE Coordinator', 'email' => "admin@{$domain}", 'phone' => '0812-55'.str_pad((string) $n, 2, '0', STR_PAD_LEFT).'-'.(1000 + $n * 37),
                'nib' => '9120'.str_pad((string) (31_000_000 + $n * 7_919), 9, '0', STR_PAD_LEFT), 'is_demo' => true,
            ]);
            $c->users()->attach($this->user('admin', 'Admin '.$name, $domain)->id, ['role' => 'admin', 'is_active' => true]);

            foreach ($stats as $year => [$hours, $fatal, $lti, $recordable]) {
                $c->safetyStatistics()->create(['year' => $year, 'manhours' => $hours, 'fatalities' => $fatal, 'lti_cases' => $lti, 'recordable_cases' => $recordable, 'lost_days' => $lti * 9]);
            }

            foreach ([
                [DocumentType::Nib, 'Nomor Induk Berusaha', null],
                [DocumentType::KebijakanK3, 'Kebijakan K3 perusahaan', null],
                [DocumentType::Bpjs, 'Sertifikat kepesertaan BPJS Ketenagakerjaan', today()->addMonths(4 + $n)],
                [DocumentType::ProgramK3, 'Program K3 tahun '.today()->year, today()->endOfYear()],
            ] as [$type, $title, $exp]) {
                $c->contractorDocuments()->create(['type' => $type, 'title' => $title, 'number' => strtoupper(substr($key, 0, 3)).'/'.(100 + $n * 11), 'issued_at' => today()->subYear(), 'expires_at' => $exp]);
            }

            foreach ($this->extraDocuments()[$key] ?? [] as [$type, $title, $number, $exp]) {
                $c->contractorDocuments()->create(['type' => $type, 'title' => $title, 'number' => $number, 'issued_at' => today()->subYears(2), 'expires_at' => $exp]);
            }

            ContractorRegistration::create(['contractor_id' => $c->id, 'vendor_code' => 'V-'.(1000 + $n), 'status' => RegistrationStatus::Menunggu]);
            $this->contractors[$key] = $c;
        }

        foreach ($this->workerData() as $key => $list) {
            foreach ($list as $i => [$name, $position, $competencies]) {
                $w = Worker::create([
                    'contractor_id' => $this->contractors[$key]->id, 'name' => $name, 'position' => $position,
                    'nik' => '35'.str_pad((string) (crc32($name) % 100_000_000_000_000), 14, '0', STR_PAD_LEFT),
                    // Pekerja terakhir rekanan pertama sengaja surat sehatnya kedaluwarsa (contoh penolakan kartu).
                    'medical_until' => $key === array_key_first($this->workerData()) && $i === count($list) - 1 ? today()->subDays(10) : today()->addMonths(6 + $i),
                ]);
                foreach ($competencies as $comp) {
                    $w->competencies()->create(['name' => $comp, 'number' => 'K/'.(4000 + $i * 13), 'issuer' => 'Kemnaker RI', 'expires_at' => today()->addYears(2)]);
                }
            }
        }
    }

    protected function seedPrequalifications(): void
    {
        $service = app(PrequalificationService::class);

        foreach ($this->pqData() as $key => [$target, $base, $criticalZero, $stage]) {
            $pq = $service->start($this->owner, $this->contractors[$key], $this->template, $target);

            foreach ($pq->answers()->with('item')->get() as $n => $answer) {
                /** @var PqAnswer $answer */
                $self = max(1, min(4, $base + ($n % 5 === 0 ? -1 : 0) + ($base < 4 && $n % 3 === 0 ? 1 : 0)));
                $answer->update(['answer' => 'Ya. '.$answer->item->evidence_hint.' terlampir dan ditinjau setiap tahun.', 'self_score' => $self]);
            }

            $service->submit($pq);

            if ($stage === 'klarifikasi') {
                $service->requestClarification($pq, $this->clarificationText($key));

                continue;
            }
            if ($stage === 'diajukan') {
                continue;
            }

            foreach ($pq->answers()->with('item')->get() as $answer) {
                $score = $answer->item->code === $criticalZero ? 0 : (int) $answer->self_score - ($base <= 3 && $answer->item->weight === 3 ? 1 : 0);
                $answer->update(['score' => max(0, min(4, $score)), 'evaluator_note' => $score === 0 ? $this->criticalNote($key) : null]);
            }

            if ($target === RiskLevel::Tinggi) {
                $pq->update(['site_visit_date' => today()->subDays(40), 'site_visit_notes' => 'Kunjungan ke kantor dan workshop rekanan: catatan inspeksi alat, gudang APD, dan arsip pelatihan diperiksa; sesuai.']);
            }

            $service->evaluate($pq, $this->users['evaluator'], 'Penilaian dokumen selesai.');
            $service->approve($pq->fresh(), $this->users['pimpinan']);
        }

        foreach ($this->pqData() as $key => [, , , $stage]) {
            $registration = $this->contractors[$key]->registrationFor($this->owner);
            if ($stage === 'ditolak') {
                $registration->update([
                    'status' => RegistrationStatus::DaftarHitam, 'blacklisted_by' => $this->users['pimpinan']->id, 'blacklisted_at' => now()->subDays(20),
                    'blacklist_reason' => $this->criticalNote($key),
                ]);
            } elseif ($registration->status === RegistrationStatus::Menunggu) {
                $registration->update(['status' => RegistrationStatus::Aktif, 'registered_at' => today()->subMonths(3)]);
            }
        }
    }

    protected function clarificationText(string $key): string
    {
        return 'Mohon lengkapi sertifikat kompetensi personel kunci dan prosedur tanggap darurat yang ditandatangani pimpinan.';
    }

    protected function criticalNote(string $key): string
    {
        return 'Kecelakaan fatal 2023 tanpa laporan investigasi dan tindak lanjut.';
    }

    /**
     * Rencana pekerjaan. Kolom per rencana: title, area, mode, days, workers, lines, flow (contract|tender|assessed),
     * contractor, invite (tender), number, value, start_offset, hse (6 nilai), technical, kickoff, bridging, induction, fail_index.
     */
    protected function seedWork(): void
    {
        $risk = app(RiskAssessor::class);
        $tenders = app(TenderService::class);
        $mob = app(MobilizationService::class);
        $rubric = array_keys(config('rekana.hse_plan_rubric'));

        foreach ($this->planData() as $key => $p) {
            $plan = WorkPlan::create([
                'title' => $p['title'], 'area_id' => $this->areas[$p['area']]->id, 'requester_id' => $this->users['pemilik']->id,
                'scope' => $p['scope'] ?? $p['title'].' sesuai spesifikasi teknis.', 'contract_mode' => $p['mode'],
                'planned_start' => today()->addDays($p['start_offset'] ?? 7), 'planned_end' => today()->addDays(($p['start_offset'] ?? 7) + $p['days']),
                'worker_estimate' => $p['workers'], 'contract_value' => $p['value'] ?? null,
            ]);
            foreach ($p['lines'] as [$activity, $hazard, $l, $s, $controls]) {
                RiskLine::create(['work_plan_id' => $plan->id, 'activity' => $activity, 'hazard' => $hazard, 'likelihood' => $l, 'severity' => $s, 'controls' => $controls]);
            }
            $risk->assess($plan, $this->users['pemilik']);

            if ($p['flow'] === 'assessed') {
                continue;
            }

            $tender = $tenders->open($plan->fresh(), [
                'title' => $p['title'], 'hse_pass_score' => $p['pass'] ?? 65,
                'closes_at' => $p['flow'] === 'tender' ? now()->addDays(6) : now()->addDays(($p['start_offset'] ?? 0) - 14),
            ]);

            foreach ($p['flow'] === 'tender' ? $p['invite'] : [$p['contractor']] as $invitee) {
                $bid = $tenders->invite($tender, $this->contractors[$invitee]);
                $tenders->submitHsePlan($bid, $plan->fresh()->risk_category === RiskLevel::Rendah ? null : "demo/hse-plan-{$key}-{$invitee}.pdf", $p['hse_summary'] ?? 'HSE Plan proyek: organisasi K3, JSA per aktivitas, daftar personel bersertifikat, rencana tanggap darurat.');
            }

            if ($p['flow'] === 'tender') {
                continue;
            }

            $bid = $tender->bids()->firstOrFail();
            $tenders->evaluateBid($bid, array_combine($rubric, $p['hse']), $this->users['evaluator'], $p['technical'] ?? 80, $p['value'] ?? null);
            $contract = $tenders->award($bid->fresh(), [
                'number' => $p['number'], 'start_date' => today()->addDays($p['start_offset']), 'end_date' => today()->addDays($p['start_offset'] + $p['days']),
                'value' => $p['value'] ?? null, 'hse_officer_id' => $this->users['pengawas']->id,
            ]);
            $mob->kickoff($contract, $p['kickoff'], $p['bridging'] ?? null);

            $induction = Induction::create([
                'contract_id' => $contract->id, 'area_id' => $plan->area_id, 'title' => $p['induction'],
                'held_at' => today()->addDays($p['start_offset'] - 2), 'trainer' => $this->users['pengawas']->name,
                'passing_score' => 70, 'valid_months' => 12, 'topics' => $this->inductionTopics(),
            ]);
            foreach ($this->contractors[$p['contractor']]->workers()->get() as $i => $worker) {
                InductionAttendee::create([
                    'induction_id' => $induction->id, 'worker_id' => $worker->id,
                    'pre_score' => 50 + ($i * 7) % 30, 'post_score' => $i === ($p['fail_index'] ?? -1) ? 60 : 80 + ($i * 3) % 20,
                ]);
            }
            foreach ($this->contractors[$p['contractor']]->workers()->get() as $worker) {
                if (! $mob->cardBlockers($contract, $worker)) {
                    $mob->issueCard($contract, $worker);
                }
            }
            $mob->approveStart($contract->fresh(), $this->users['pimpinan']);
            $this->contracts[$key] = $contract->fresh();
        }
    }

    protected function seedPermits(): void
    {
        $service = app(PermitService::class);

        foreach ($this->permitData() as [$planKey, $type, $title, $names, $start, $hours, $gas, $isolation, $final]) {
            /** @var PermitType $type */
            $contract = $this->contracts[$planKey];
            $checklist = collect($type->checklist())->map(fn () => true)->all();
            if ($final === 'draf') {
                $checklist = array_slice($checklist, 0, 3, true);
            }

            $permit = WorkPermit::create([
                'contract_id' => $contract->id, 'area_id' => $contract->workPlan->area_id, 'type' => $type, 'title' => $title,
                'description' => $title.'.', 'location_detail' => $contract->workPlan->area->name,
                'starts_at' => $start, 'ends_at' => $start->copy()->addHours($hours),
                'checklist' => $checklist, 'gas_test' => $gas, 'isolation_points' => $isolation, 'requested_by' => $this->users['pemilik']->id,
            ]);
            /** @var Collection<int, Worker> $workers */
            $workers = $contract->contractor->workers()->whereIn('name', $names)->get();
            $permit->workers()->attach($workers->pluck('id')->all());

            if ($final === 'draf') {
                continue;
            }

            if ($final === 'ditutup') {
                // Izin yang sudah lewat: dicatat langsung sebagai riwayat.
                $permit->forceFill([
                    'status' => PermitStatus::Ditutup, 'approved_by' => $this->users['pengawas']->id, 'approved_at' => $start,
                    'activated_at' => $start, 'closed_by' => $this->users['pengawas']->id, 'closed_at' => $start->copy()->addHours($hours - 1),
                    'close_notes' => 'Pekerjaan selesai, area dirapikan, isolasi dilepas dan diverifikasi.',
                ])->save();

                continue;
            }

            $service->submit($permit, $this->users['pemilik']);
            if ($final === 'aktif') {
                $service->approve($permit->fresh(), $this->users['pengawas']);
                $service->activate($permit->fresh());
            }
        }
    }
}
