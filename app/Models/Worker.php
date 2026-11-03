<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Pekerja rekanan. Tidak perlu akun; cukup kartu akses QR. */
class Worker extends Model
{
    protected $attributes = ['is_active' => true];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'medical_until' => 'date', 'is_active' => 'boolean'];
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(WorkerCompetency::class);
    }

    public function inductionAttendances(): HasMany
    {
        return $this->hasMany(InductionAttendee::class)->withoutGlobalScopes();
    }

    public function accessCards(): HasMany
    {
        return $this->hasMany(AccessCard::class)->withoutGlobalScopes();
    }

    public function medicalValid(): bool
    {
        return $this->medical_until !== null && $this->medical_until->endOfDay()->isFuture();
    }

    /** Kehadiran induksi lulus yang masih berlaku di owner tertentu. */
    public function validInduction(Owner|int $owner): ?InductionAttendee
    {
        $id = $owner instanceof Owner ? $owner->id : $owner;

        return $this->inductionAttendances()->where('owner_id', $id)->where('passed', true)
            ->whereDate('valid_until', '>=', today())->orderByDesc('valid_until')->first();
    }

    public function activeCard(Owner|int $owner): ?AccessCard
    {
        $id = $owner instanceof Owner ? $owner->id : $owner;

        return $this->accessCards()->where('owner_id', $id)->usable()->orderByDesc('valid_until')->first();
    }
}
