<?php

namespace App\Models;

use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use App\Support\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Izin kerja (Permit to Work) dasar: kerja panas, ruang terbatas, ketinggian, LOTO, umum. */
class WorkPermit extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status'];

    protected array $ownerParents = ['contract_id' => Contract::class, 'area_id' => Area::class];

    protected function casts(): array
    {
        return [
            'type' => PermitType::class,
            'status' => PermitStatus::class,
            'checklist' => 'array',
            'gas_test' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'approved_at' => 'datetime',
            'activated_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WorkPermit $permit) {
            $permit->status ??= PermitStatus::Draf;
            $permit->number ??= Sequence::next('permit', $permit->owner_id ?? app(\App\Support\CurrentOwner::class)->id());
        });
    }

    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function closer(): BelongsTo { return $this->belongsTo(User::class, 'closed_by'); }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'permit_workers');
    }
}
