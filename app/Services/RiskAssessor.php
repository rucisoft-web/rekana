<?php

namespace App\Services;

use App\Enums\RiskLevel;
use App\Enums\WorkPlanStatus;
use App\Models\RiskLine;
use App\Models\User;
use App\Models\WorkPlan;
use App\Support\RuleViolation;

/**
 * M1: kategori risiko pekerjaan = skor tertinggi (kemungkinan x keparahan) dari semua aktivitas,
 * dinaikkan ke Tinggi bila ada bahaya tertentu (config rekana.risk.force_high).
 */
class RiskAssessor
{
    /** @return array{score: int, category: RiskLevel, forced_by: array<int, string>} */
    public function evaluate(WorkPlan $plan): array
    {
        $lines = $plan->riskLines()->get();
        $score = (int) $lines->max('score');
        $forced = $lines->filter(fn (RiskLine $l) => $l->hazard->forcesHigh())
            ->map(fn (RiskLine $l) => $l->hazard->value)->unique()->values()->all();

        $category = $forced !== [] ? RiskLevel::Tinggi : RiskLevel::fromScore($score);

        return ['score' => $score, 'category' => $category, 'forced_by' => $forced];
    }

    public function assess(WorkPlan $plan, ?User $by = null): WorkPlan
    {
        RuleViolation::throwIf([
            $plan->riskLines()->doesntExist() ? 'Tambahkan minimal satu aktivitas dan bahayanya sebelum menilai risiko.' : null,
            $plan->contract_mode === null ? 'Pilih mode kontrak IOGP (Mode 1, 2, atau 3).' : null,
            in_array($plan->status, [WorkPlanStatus::Terkontrak, WorkPlanStatus::Selesai], true) ? 'Pekerjaan yang sudah terkontrak tidak dapat dinilai ulang.' : null,
        ]);

        $result = $this->evaluate($plan);

        $plan->forceFill([
            'risk_score' => $result['score'],
            'risk_category' => $result['category'],
            'forced_by' => $result['forced_by'],
            'status' => $plan->status === WorkPlanStatus::Draf ? WorkPlanStatus::Dinilai : $plan->status,
            'assessed_by' => $by?->id ?? auth()->id(),
            'assessed_at' => now(),
        ])->save();

        return $plan;
    }

    /** Kewajiban tahap menurut kategori (tabel Aturan bisnis di rancangan). */
    public static function obligations(RiskLevel $level): array
    {
        return match ($level) {
            RiskLevel::Rendah => ['pq' => 'Ringkas (butir inti)', 'hse_plan' => 'Tidak wajib', 'bridging' => 'Tidak', 'inspeksi' => 'Bulanan'],
            RiskLevel::Menengah => ['pq' => 'Lengkap', 'hse_plan' => 'Wajib', 'bridging' => 'Jika Mode 2', 'inspeksi' => 'Mingguan'],
            RiskLevel::Tinggi => ['pq' => 'Lengkap + kunjungan lokasi', 'hse_plan' => 'Wajib + presentasi', 'bridging' => 'Wajib', 'inspeksi' => 'Harian'],
        };
    }
}
