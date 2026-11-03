<?php

namespace App\Services;

use App\Enums\PqStatus;
use App\Enums\RegistrationStatus;
use App\Enums\RiskLevel;
use App\Models\Contractor;
use App\Models\CsmsCertificate;
use App\Models\Owner;
use App\Models\PqAnswer;
use App\Models\Prequalification;
use App\Models\QuestionnaireTemplate;
use App\Models\TemplateItem;
use App\Models\User;
use App\Support\RuleViolation;
use App\Support\Sequence;
use Illuminate\Support\Facades\DB;

/**
 * M2: alur prakualifikasi.
 *
 * draf (rekanan mengisi) → diajukan → [klarifikasi → diajukan] → dinilai (evaluator) → disetujui | ditolak (pimpinan).
 * Skor = Σ(bobot x skor) / Σ(bobot x skor maks) x 100%. Butir kritis bernilai 0 menggagalkan PQ.
 */
class PrequalificationService
{
    public function start(Owner $owner, Contractor $contractor, QuestionnaireTemplate $template, RiskLevel $target): Prequalification
    {
        $registration = $contractor->registrationFor($owner);

        RuleViolation::throwIf([
            ! $registration || $registration->status === RegistrationStatus::DaftarHitam ? 'Rekanan belum terdaftar atau masuk daftar hitam di owner ini.' : null,
            $template->owner_id !== null && $template->owner_id !== $owner->id ? 'Template tidak tersedia untuk owner ini.' : null,
            Prequalification::withoutGlobalScopes()->where('owner_id', $owner->id)->where('contractor_id', $contractor->id)
                ->whereIn('status', [PqStatus::Draf, PqStatus::Diajukan, PqStatus::Klarifikasi, PqStatus::Dinilai])->exists()
                ? 'Rekanan masih punya prakualifikasi yang sedang berjalan di owner ini.' : null,
        ]);

        return DB::transaction(function () use ($owner, $contractor, $template, $target) {
            $pq = Prequalification::create([
                'owner_id' => $owner->id,
                'contractor_id' => $contractor->id,
                'questionnaire_template_id' => $template->id,
                'target_risk' => $target,
            ]);

            foreach ($template->items()->get()->filter(fn (TemplateItem $i) => $i->appliesTo($target)) as $item) {
                PqAnswer::create(['owner_id' => $owner->id, 'prequalification_id' => $pq->id, 'template_item_id' => $item->id]);
            }

            return $pq;
        });
    }

    public function submit(Prequalification $pq): Prequalification
    {
        $missing = $pq->answers()->with('item')->get()
            ->filter(fn (PqAnswer $a) => blank($a->answer) && ! $a->evidence_path && ! $a->contractor_document_id)
            ->map(fn (PqAnswer $a) => $a->item->code)->values();

        RuleViolation::throwIf([
            ! $pq->isEditableByContractor() ? 'Prakualifikasi ini sudah diajukan.' : null,
            $missing->isNotEmpty() ? 'Butir belum dijawab: '.$missing->take(10)->implode(', ').($missing->count() > 10 ? ', …' : '').'.' : null,
        ]);

        $pq->forceFill(['status' => PqStatus::Diajukan, 'submitted_at' => now(), 'clarification_request' => null])->save();

        return $pq;
    }

    public function requestClarification(Prequalification $pq, string $message): Prequalification
    {
        RuleViolation::throwIf([$pq->status !== PqStatus::Diajukan ? 'Klarifikasi hanya untuk PQ yang sedang dinilai.' : null]);
        $pq->forceFill(['status' => PqStatus::Klarifikasi, 'clarification_request' => $message])->save();

        return $pq;
    }

    /** @return array{score: float, critical_failed: bool, result: ?RiskLevel, unscored: int} */
    public function calculate(Prequalification $pq): array
    {
        $max = (int) config('rekana.pq.max_item_score');
        $answers = $pq->answers()->with('item')->get();
        $weightTotal = $answers->sum(fn (PqAnswer $a) => $a->item->weight * $max);
        $earned = $answers->sum(fn (PqAnswer $a) => $a->item->weight * (int) $a->score);
        $score = $weightTotal > 0 ? round($earned / $weightTotal * 100, 2) : 0.0;
        $critical = $answers->contains(fn (PqAnswer $a) => $a->item->is_critical && $a->score !== null && (int) $a->score === 0);

        return [
            'score' => $score,
            'critical_failed' => $critical,
            'result' => $critical ? null : $this->resultFor($score, $pq->target_risk, $pq->owner ?? Owner::find($pq->owner_id)),
            'unscored' => $answers->whereNull('score')->count(),
        ];
    }

    /** Kategori tertinggi yang ambangnya terpenuhi, dibatasi target yang diajukan rekanan. */
    public function resultFor(float $score, RiskLevel $target, Owner $owner): ?RiskLevel
    {
        $thresholds = $owner->pqThresholds();
        $result = null;

        foreach (RiskLevel::cases() as $level) {
            if ($score >= (float) $thresholds[$level->value] && $target->covers($level)) {
                $result = $level;
            }
        }

        return $result;
    }

    public function evaluate(Prequalification $pq, User $evaluator, ?string $notes = null): Prequalification
    {
        $calc = $this->calculate($pq);

        RuleViolation::throwIf([
            $pq->status !== PqStatus::Diajukan ? 'Hanya PQ berstatus Diajukan yang dapat dinilai.' : null,
            $calc['unscored'] > 0 ? "Masih ada {$calc['unscored']} butir yang belum diberi skor." : null,
            $pq->target_risk === RiskLevel::Tinggi && ! $pq->site_visit_date ? 'PQ risiko Tinggi wajib kunjungan lokasi; isi tanggal dan catatan kunjungan.' : null,
        ]);

        $pq->forceFill([
            'status' => PqStatus::Dinilai,
            'score' => $calc['score'],
            'critical_failed' => $calc['critical_failed'],
            'result_risk' => $calc['result'],
            'evaluator_id' => $evaluator->id,
            'evaluated_at' => now(),
            'notes' => $notes ?? $pq->notes,
        ])->save();

        return $pq;
    }

    /** Persetujuan pimpinan (empat mata: bukan evaluator yang sama). Lulus → sertifikat CSMS terbit. */
    public function approve(Prequalification $pq, User $approver): ?CsmsCertificate
    {
        RuleViolation::throwIf([
            $pq->status !== PqStatus::Dinilai ? 'Hanya PQ yang sudah dinilai yang dapat disetujui.' : null,
            $pq->evaluator_id === $approver->id ? 'Persetujuan harus oleh orang lain, bukan evaluator PQ ini.' : null,
        ]);

        return DB::transaction(function () use ($pq, $approver) {
            $passed = $pq->result_risk !== null;
            $pq->forceFill([
                'status' => $passed ? PqStatus::Disetujui : PqStatus::Ditolak,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ])->save();

            if (! $passed) {
                return null;
            }

            $registration = $pq->contractor->registrationFor($pq->owner_id);
            if ($registration && $registration->status === RegistrationStatus::Menunggu) {
                $registration->update(['status' => RegistrationStatus::Aktif, 'registered_at' => today()]);
            }

            return CsmsCertificate::create([
                'owner_id' => $pq->owner_id,
                'contractor_id' => $pq->contractor_id,
                'prequalification_id' => $pq->id,
                'number' => Sequence::next('certificate', $pq->owner_id),
                'max_risk' => $pq->result_risk,
                'score' => $pq->score,
                'issued_at' => today(),
                'valid_until' => today()->addYears((int) config('rekana.pq.certificate_years'))->subDay(),
            ]);
        });
    }

    public function revoke(CsmsCertificate $certificate, string $reason): void
    {
        $certificate->forceFill(['revoked_at' => now(), 'revoke_reason' => $reason])->save();
    }
}
