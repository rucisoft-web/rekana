<?php

namespace App\Models;

use App\Enums\BidStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Rekanan yang diundang ke seleksi beserta HSE Plan dan nilainya. */
class TenderBid extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status', 'hse_score', 'is_winner'];

    protected array $ownerParents = ['tender_id' => Tender::class];

    protected function casts(): array
    {
        return [
            'status' => BidStatus::class,
            'hse_scores' => 'array',
            'hse_score' => 'decimal:2',
            'hse_passed' => 'boolean',
            'technical_score' => 'decimal:2',
            'price' => 'decimal:2',
            'is_winner' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function tender(): BelongsTo { return $this->belongsTo(Tender::class); }
    public function contractor(): BelongsTo { return $this->belongsTo(Contractor::class); }
    public function evaluator(): BelongsTo { return $this->belongsTo(User::class, 'evaluator_id'); }

    public function auditLabel(): string
    {
        return ($this->tender?->number ?? '').' '.$this->contractor?->name;
    }
}
