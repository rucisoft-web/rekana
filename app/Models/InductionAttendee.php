<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Peserta induksi: nilai pre/post test, lulus bila post test >= nilai lulus sesi. */
class InductionAttendee extends Model
{
    use BelongsToOwner;

    protected $guarded = ['id'];

    protected array $ownerParents = ['induction_id' => Induction::class];

    protected function casts(): array
    {
        return ['passed' => 'boolean', 'valid_until' => 'date'];
    }

    protected static function booted(): void
    {
        static::saving(function (InductionAttendee $a) {
            $induction = $a->induction ?? Induction::withoutGlobalScopes()->find($a->induction_id);
            $a->passed = $a->post_score !== null && $induction && $a->post_score >= $induction->passing_score;
            $a->valid_until = $a->passed ? $induction->held_at->copy()->addMonths($induction->valid_months) : null;
        });
    }

    public function induction(): BelongsTo { return $this->belongsTo(Induction::class); }
    public function worker(): BelongsTo { return $this->belongsTo(Worker::class); }
}
