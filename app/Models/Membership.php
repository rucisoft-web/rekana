<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Keanggotaan pengguna di satu owner: peran dan area tugas. */
class Membership extends Model
{
    use Auditable, BelongsToOwner;

    protected $attributes = ['is_active' => true];

    protected $guarded = ['id'];

    protected array $tracked = ['role', 'is_active', 'area_id'];

    protected array $ownerParents = ['area_id' => Area::class];

    protected function casts(): array
    {
        return ['role' => Role::class, 'is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function auditLabel(): string
    {
        return ($this->user?->name ?? 'Pengguna').' · '.$this->role?->getLabel();
    }
}
