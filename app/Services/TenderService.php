<?php

namespace App\Services;

use App\Enums\BidStatus;
use App\Enums\ContractStatus;
use App\Enums\RegistrationStatus;
use App\Enums\RiskLevel;
use App\Enums\TenderStatus;
use App\Enums\WorkPlanStatus;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\ContractorRegistration;
use App\Models\Tender;
use App\Models\TenderBid;
use App\Models\User;
use App\Models\WorkPlan;
use App\Support\RuleViolation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * M3: seleksi. Hanya rekanan aktif dengan sertifikat CSMS berlaku yang mencakup kategori risiko pekerjaan
 * yang dapat diundang. HSE Plan dinilai dengan rubrik 0-4 per kriteria; lulus bila skor >= ambang seleksi.
 */
class TenderService
{
    public function open(WorkPlan $plan, array $data): Tender
    {
        RuleViolation::throwIf([
            $plan->risk_category === null ? 'Nilai risiko pekerjaan (M1) terlebih dahulu.' : null,
            in_array($plan->status, [WorkPlanStatus::Terkontrak, WorkPlanStatus::Selesai], true) ? 'Pekerjaan ini sudah terkontrak.' : null,
        ]);

        $tender = Tender::create($data + ['owner_id' => $plan->owner_id, 'work_plan_id' => $plan->id, 'status' => TenderStatus::Dibuka]);
        $plan->update(['status' => WorkPlanStatus::Tender]);

        return $tender;
    }

    /** @return Collection<int, Contractor> rekanan yang memenuhi syarat diundang. */
    public function eligibleContractors(Tender $tender): Collection
    {
        $category = $tender->workPlan->risk_category;

        return ContractorRegistration::withoutGlobalScopes()->with('contractor')
            ->where('owner_id', $tender->owner_id)->where('status', RegistrationStatus::Aktif)->get()
            ->map->contractor
            ->filter(fn (Contractor $c) => $c->is_active && $c->validCertificate($tender->owner_id)?->max_risk->covers($category))
            ->values();
    }

    public function invite(Tender $tender, Contractor $contractor): TenderBid
    {
        $certificate = $contractor->validCertificate($tender->owner_id);
        $category = $tender->workPlan->risk_category;
        $registration = $contractor->registrationFor($tender->owner_id);

        RuleViolation::throwIf([
            ! in_array($tender->status, [TenderStatus::Draf, TenderStatus::Dibuka], true) ? 'Seleksi sudah ditutup.' : null,
            $registration?->status !== RegistrationStatus::Aktif ? "{$contractor->name} bukan rekanan aktif." : null,
            ! $certificate ? "{$contractor->name} tidak punya sertifikat CSMS yang berlaku." : null,
            $certificate && ! $certificate->max_risk->covers($category)
                ? "Sertifikat {$contractor->name} hanya sampai risiko {$certificate->max_risk->getLabel()}, pekerjaan ini risiko {$category->getLabel()}." : null,
            $tender->bids()->where('contractor_id', $contractor->id)->exists() ? "{$contractor->name} sudah diundang." : null,
        ]);

        return TenderBid::create(['owner_id' => $tender->owner_id, 'tender_id' => $tender->id, 'contractor_id' => $contractor->id, 'status' => BidStatus::Diundang]);
    }

    public function submitHsePlan(TenderBid $bid, ?string $path, ?string $summary): TenderBid
    {
        $needsPlan = $bid->tender->workPlan->risk_category !== RiskLevel::Rendah;

        RuleViolation::throwIf([
            $bid->tender->status !== TenderStatus::Dibuka ? 'Seleksi tidak sedang dibuka.' : null,
            ! in_array($bid->status, [BidStatus::Diundang, BidStatus::Diajukan], true) ? 'Penawaran ini sudah dinilai.' : null,
            $needsPlan && ! $path ? 'HSE Plan wajib diunggah untuk pekerjaan risiko Menengah dan Tinggi.' : null,
        ]);

        $bid->forceFill(['hse_plan_path' => $path, 'hse_plan_summary' => $summary, 'status' => BidStatus::Diajukan, 'submitted_at' => now()])->save();

        return $bid;
    }

    /** @param array<string, int> $scores kunci rubrik => 0-4 */
    public function evaluateBid(TenderBid $bid, array $scores, User $evaluator, ?float $technical = null, ?float $price = null, ?string $notes = null): TenderBid
    {
        $rubric = array_keys(config('rekana.hse_plan_rubric'));
        $scores = array_intersect_key(array_map('intval', $scores), array_flip($rubric));
        $max = (int) config('rekana.pq.max_item_score');

        RuleViolation::throwIf([
            $bid->status !== BidStatus::Diajukan ? 'Hanya penawaran yang sudah mengajukan HSE Plan yang dapat dinilai.' : null,
            count($scores) !== count($rubric) ? 'Semua kriteria rubrik HSE Plan wajib diberi nilai.' : null,
        ]);

        $percent = round(array_sum($scores) / (count($rubric) * $max) * 100, 2);

        $bid->forceFill([
            'hse_scores' => $scores,
            'hse_score' => $percent,
            'hse_passed' => $percent >= $bid->tender->hse_pass_score,
            'technical_score' => $technical,
            'price' => $price,
            'notes' => $notes,
            'evaluator_id' => $evaluator->id,
            'status' => BidStatus::Dinilai,
        ])->save();

        if ($bid->tender->status === TenderStatus::Dibuka) {
            $bid->tender->update(['status' => TenderStatus::Evaluasi]);
        }

        return $bid;
    }

    /** Nilai gabungan untuk peringkat: bobot HSE (persen) x skor HSE + sisanya x skor teknis. */
    public static function combinedScore(TenderBid $bid): ?float
    {
        if ($bid->hse_score === null) {
            return null;
        }
        $w = $bid->tender->hse_weight / 100;

        return round($w * (float) $bid->hse_score + (1 - $w) * (float) ($bid->technical_score ?? 0), 2);
    }

    public function award(TenderBid $bid, array $contract): Contract
    {
        $tender = $bid->tender;

        RuleViolation::throwIf([
            in_array($tender->status, [TenderStatus::Selesai, TenderStatus::Batal], true) ? 'Seleksi sudah selesai atau batal.' : null,
            $bid->status !== BidStatus::Dinilai ? 'Penawaran belum dinilai.' : null,
            ! $bid->hse_passed ? 'HSE Plan rekanan ini di bawah ambang lulus; tidak dapat ditetapkan sebagai pemenang.' : null,
            ! $bid->contractor->validCertificate($tender->owner_id) ? 'Sertifikat CSMS rekanan sudah tidak berlaku.' : null,
        ]);

        return DB::transaction(function () use ($bid, $tender, $contract) {
            $tender->bids()->whereKeyNot($bid->id)->update(['status' => BidStatus::Kalah->value, 'is_winner' => false]);
            $bid->forceFill(['status' => BidStatus::Menang, 'is_winner' => true])->save();
            $tender->update(['status' => TenderStatus::Selesai]);
            $tender->workPlan->update(['status' => WorkPlanStatus::Terkontrak]);

            return Contract::create($contract + [
                'owner_id' => $tender->owner_id,
                'work_plan_id' => $tender->work_plan_id,
                'tender_id' => $tender->id,
                'contractor_id' => $bid->contractor_id,
                'title' => $contract['title'] ?? $tender->title,
                'status' => ContractStatus::Mobilisasi,
            ]);
        });
    }
}
