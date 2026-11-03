<?php

namespace App\Models;

use App\Enums\PqStatus;
use App\Enums\RiskLevel;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use App\Support\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** M2: prakualifikasi satu rekanan di satu owner, dengan kuesioner dari template. */
class Prequalification extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status', 'score', 'result_risk', 'target_risk'];

    protected function casts(): array
    {
        return [
            'status' => PqStatus::class,
            'target_risk' => RiskLevel::class,
            'result_risk' => RiskLevel::class,
            'critical_failed' => 'boolean',
            'score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'evaluated_at' => 'datetime',
            'approved_at' => 'datetime',
            'site_visit_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Prequalification $pq) {
            $pq->status ??= PqStatus::Draf;
            $pq->number ??= Sequence::next('pq', $pq->owner_id ?? app(\App\Support\CurrentOwner::class)->id());
        });
    }

    public function contractor(): BelongsTo { return $this->belongsTo(Contractor::class); }
    public function template(): BelongsTo { return $this->belongsTo(QuestionnaireTemplate::class, 'questionnaire_template_id'); }
    public function evaluator(): BelongsTo { return $this->belongsTo(User::class, 'evaluator_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function answers(): HasMany { return $this->hasMany(PqAnswer::class); }
    public function certificate(): HasOne { return $this->hasOne(CsmsCertificate::class); }

    public function isEditableByContractor(): bool
    {
        return in_array($this->status, [PqStatus::Draf, PqStatus::Klarifikasi], true);
    }

    public function auditLabel(): string
    {
        return $this->number.' '.$this->contractor?->name;
    }
}
