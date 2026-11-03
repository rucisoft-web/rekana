<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Status rekanan di satu owner (daftar rekanan terdaftar, penangguhan, daftar hitam). */
class ContractorRegistration extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['status', 'vendor_code'];

    protected function casts(): array
    {
        return ['status' => RegistrationStatus::class, 'registered_at' => 'date', 'blacklisted_at' => 'datetime'];
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function auditLabel(): string
    {
        return (string) $this->contractor?->name;
    }
}
