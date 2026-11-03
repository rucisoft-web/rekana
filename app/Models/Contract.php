<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kontrak aktif hasil seleksi (atau penunjukan langsung). Induk induksi, kartu akses, dan izin kerja. */
class Contract extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status', 'start_approved_at', 'end_date'];

    protected array $ownerParents = ['work_plan_id' => WorkPlan::class, 'tender_id' => Tender::class];

    protected function casts(): array
    {
        return [
            'status' => ContractStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'value' => 'decimal:2',
            'kickoff_at' => 'datetime',
            'start_approved_at' => 'datetime',
        ];
    }

    public function workPlan(): BelongsTo { return $this->belongsTo(WorkPlan::class); }
    public function contractor(): BelongsTo { return $this->belongsTo(Contractor::class); }
    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
    public function hseOfficer(): BelongsTo { return $this->belongsTo(User::class, 'hse_officer_id'); }
    public function startApprover(): BelongsTo { return $this->belongsTo(User::class, 'start_approved_by'); }
    public function accessCards(): HasMany { return $this->hasMany(AccessCard::class); }
    public function inductions(): HasMany { return $this->hasMany(Induction::class); }
    public function workPermits(): HasMany { return $this->hasMany(WorkPermit::class); }

    public function isWorkable(): bool
    {
        return $this->status === ContractStatus::Berjalan && $this->start_approved_at !== null
            && $this->end_date->endOfDay()->isFuture();
    }
}
