<?php

namespace App\Models;

use App\Enums\RiskLevel;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/** Sertifikat CSMS: rekanan lulus PQ dan boleh mengerjakan pekerjaan sampai kategori max_risk. */
class CsmsCertificate extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['valid_until', 'revoked_at'];

    protected function casts(): array
    {
        return ['max_risk' => RiskLevel::class, 'score' => 'decimal:2', 'issued_at' => 'date', 'valid_until' => 'date', 'revoked_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (CsmsCertificate $c) => $c->verify_code ??= (string) Str::uuid());
    }

    public function contractor(): BelongsTo { return $this->belongsTo(Contractor::class); }
    public function prequalification(): BelongsTo { return $this->belongsTo(Prequalification::class); }

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('revoked_at')->whereDate('valid_until', '>=', today());
    }

    public function isValid(): bool
    {
        return $this->revoked_at === null && $this->valid_until->endOfDay()->isFuture();
    }

    public function statusLabel(): string
    {
        return match (true) {
            $this->revoked_at !== null => 'Dicabut',
            ! $this->isValid() => 'Kedaluwarsa',
            default => 'Berlaku',
        };
    }

    public function auditLabel(): string
    {
        return $this->number.' '.$this->contractor?->name;
    }
}
