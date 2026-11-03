<?php

namespace App\Models;

use App\Enums\Hazard;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu baris matriks risiko 5x5: aktivitas, bahaya, kemungkinan x keparahan. */
class RiskLine extends Model
{
    use BelongsToOwner;

    protected $guarded = ['id'];

    protected array $ownerParents = ['work_plan_id' => WorkPlan::class];

    protected function casts(): array
    {
        return ['hazard' => Hazard::class];
    }

    protected static function booted(): void
    {
        static::saving(function (RiskLine $line) {
            $line->likelihood = max(1, min(5, (int) $line->likelihood));
            $line->severity = max(1, min(5, (int) $line->severity));
            $line->score = $line->likelihood * $line->severity;
        });
    }

    public function workPlan(): BelongsTo
    {
        return $this->belongsTo(WorkPlan::class);
    }
}
