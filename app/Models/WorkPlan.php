<?php

namespace App\Models;

use App\Enums\ContractMode;
use App\Enums\RiskLevel;
use App\Enums\WorkPlanStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use App\Support\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** M1: rencana pekerjaan yang akan dikontrakkan beserta hasil penilaian risikonya. */
class WorkPlan extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status', 'risk_category', 'contract_mode'];

    protected array $ownerParents = ['area_id' => Area::class];

    protected function casts(): array
    {
        return [
            'planned_start' => 'date',
            'planned_end' => 'date',
            'contract_value' => 'decimal:2',
            'subcontracting' => 'boolean',
            'contract_mode' => ContractMode::class,
            'risk_category' => RiskLevel::class,
            'status' => WorkPlanStatus::class,
            'forced_by' => 'array',
            'assessed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WorkPlan $plan) {
            $plan->status ??= WorkPlanStatus::Draf;
            $plan->number ??= Sequence::next('work_plan', $plan->owner_id ?? app(\App\Support\CurrentOwner::class)->id());
        });
    }

    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requester_id'); }
    public function assessor(): BelongsTo { return $this->belongsTo(User::class, 'assessed_by'); }
    public function riskLines(): HasMany { return $this->hasMany(RiskLine::class); }
    public function tenders(): HasMany { return $this->hasMany(Tender::class); }
    public function contracts(): HasMany { return $this->hasMany(Contract::class); }
}
