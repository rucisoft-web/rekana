<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Berkas perusahaan rekanan dengan tanggal kedaluwarsa (pustaka dokumen). */
class ContractorDocument extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['type' => DocumentType::class, 'issued_at' => 'date', 'expires_at' => 'date'];
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->endOfDay()->isPast();
    }

    public function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        return $query->whereNotNull('expires_at')->whereBetween('expires_at', [today(), today()->addDays($days)]);
    }
}
