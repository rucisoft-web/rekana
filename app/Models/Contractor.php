<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Perusahaan rekanan (kontraktor). Dipakai bersama antar-owner: dokumen, pekerja, dan statistik K3
 * cukup diisi sekali di portal kontraktor lalu terlihat oleh owner tempat rekanan terdaftar.
 */
class Contractor extends Model implements HasName
{
    protected $attributes = ['is_active' => true, 'is_demo' => false];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_demo' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Contractor $contractor) {
            $base = Str::slug($contractor->name) ?: 'rekanan';
            $slug = $base;

            for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }

            $contractor->slug ??= $slug;
        });
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contractor_users')->withPivot(['role', 'is_active'])->withTimestamps();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ContractorRegistration::class)->withoutGlobalScopes();
    }

    public function registrationFor(Owner|int $owner): ?ContractorRegistration
    {
        $id = $owner instanceof Owner ? $owner->id : $owner;

        return ContractorRegistration::withoutGlobalScopes()->where('owner_id', $id)->where('contractor_id', $this->id)->first();
    }

    /** Sertifikat CSMS yang masih berlaku di owner tertentu (terbaru, kategori tertinggi). */
    public function validCertificate(Owner|int $owner): ?CsmsCertificate
    {
        $id = $owner instanceof Owner ? $owner->id : $owner;

        return CsmsCertificate::withoutGlobalScopes()->where('owner_id', $id)->where('contractor_id', $this->id)
            ->valid()->get()->sortByDesc(fn (CsmsCertificate $c) => $c->max_risk->rank())->first();
    }

    /** @return array{manhours: int, ltifr: ?float, trir: ?float, fatalities: int} rekap 3 tahun terakhir */
    public function safetySummary(int $years = 3): array
    {
        $rows = $this->safetyStatistics()->orderByDesc('year')->limit($years)->get();
        $hours = (int) $rows->sum('manhours');

        return [
            'manhours' => $hours,
            'ltifr' => $hours > 0 ? round($rows->sum('lti_cases') * 1_000_000 / $hours, 2) : null,
            'trir' => $hours > 0 ? round($rows->sum('recordable_cases') * 200_000 / $hours, 2) : null,
            'fatalities' => (int) $rows->sum('fatalities'),
        ];
    }

    // Relasi yang dibutuhkan Filament untuk resource bertenant di portal kontraktor.
    public function contractorDocuments(): HasMany { return $this->hasMany(ContractorDocument::class); }
    public function safetyStatistics(): HasMany { return $this->hasMany(SafetyStatistic::class); }
    public function workers(): HasMany { return $this->hasMany(Worker::class); }
    public function prequalifications(): HasMany { return $this->hasMany(Prequalification::class)->withoutGlobalScopes(); }
    public function csmsCertificates(): HasMany { return $this->hasMany(CsmsCertificate::class)->withoutGlobalScopes(); }
    public function tenderBids(): HasMany { return $this->hasMany(TenderBid::class)->withoutGlobalScopes(); }
    public function contracts(): HasMany { return $this->hasMany(Contract::class)->withoutGlobalScopes(); }
    public function contractorRegistrations(): HasMany { return $this->registrations(); }
}
