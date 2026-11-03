<?php

namespace App\Models;

use App\Enums\TenderStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use App\Support\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** M3: seleksi kontraktor untuk satu rencana pekerjaan, dengan penilaian HSE Plan. */
class Tender extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status'];

    protected array $ownerParents = ['work_plan_id' => WorkPlan::class];

    protected function casts(): array
    {
        return ['status' => TenderStatus::class, 'closes_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Tender $tender) {
            $tender->status ??= TenderStatus::Draf;
            $tender->number ??= Sequence::next('tender', $tender->owner_id ?? app(\App\Support\CurrentOwner::class)->id());
        });
    }

    public function workPlan(): BelongsTo { return $this->belongsTo(WorkPlan::class); }
    public function bids(): HasMany { return $this->hasMany(TenderBid::class); }
    public function contract(): HasOne { return $this->hasOne(Contract::class); }
}
