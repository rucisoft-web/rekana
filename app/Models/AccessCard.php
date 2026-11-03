<?php

namespace App\Models;

use App\Enums\CardStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/** Kartu akses QR pekerja untuk satu kontrak; berlaku sampai tanggal terpendek di antara syaratnya. */
class AccessCard extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status', 'valid_until'];

    protected array $ownerParents = ['contract_id' => Contract::class];

    protected function casts(): array
    {
        return ['status' => CardStatus::class, 'issued_at' => 'date', 'valid_until' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(fn (AccessCard $c) => $c->code ??= (string) Str::uuid());
    }

    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function worker(): BelongsTo { return $this->belongsTo(Worker::class); }

    public function scopeUsable(Builder $query): Builder
    {
        return $query->where('status', CardStatus::Aktif)->whereDate('valid_until', '>=', today());
    }

    public function isUsable(): bool
    {
        return $this->status === CardStatus::Aktif && $this->valid_until->endOfDay()->isFuture();
    }

    public function auditLabel(): string
    {
        return 'Kartu '.$this->worker?->name;
    }
}
