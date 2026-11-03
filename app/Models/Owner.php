<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/** Owner / pemberi kerja (tenant), misalnya satu pabrik. Seluruh data CSMS owner berinduk ke sini. */
class Owner extends Model implements HasName
{
    protected $attributes = ['is_active' => true, 'is_demo' => false, 'accepts_registration' => true];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
            'is_demo' => 'boolean',
            'accepts_registration' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Owner $owner) {
            $base = Str::slug($owner->code ?: $owner->name) ?: 'owner';
            $slug = $base;

            for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }

            $owner->slug ??= $slug;
        });
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    /** Ambang skor PQ (persen) per kategori risiko: pengaturan owner, atau nilai awal config. */
    public function pqThresholds(): array
    {
        return array_merge(config('rekana.pq.thresholds'), $this->settings['pq_thresholds'] ?? []);
    }

    public function contractors(): BelongsToMany
    {
        return $this->belongsToMany(Contractor::class, 'contractor_registrations')->withPivot(['status', 'vendor_code']);
    }

    // Relasi yang dibutuhkan Filament untuk resource bertenant (nama jamak dari model).
    public function memberships(): HasMany { return $this->hasMany(Membership::class); }
    public function areas(): HasMany { return $this->hasMany(Area::class); }
    public function contractorRegistrations(): HasMany { return $this->hasMany(ContractorRegistration::class); }
    public function questionnaireTemplates(): HasMany { return $this->hasMany(QuestionnaireTemplate::class); }
    public function workPlans(): HasMany { return $this->hasMany(WorkPlan::class); }
    public function riskLines(): HasMany { return $this->hasMany(RiskLine::class); }
    public function prequalifications(): HasMany { return $this->hasMany(Prequalification::class); }
    public function pqAnswers(): HasMany { return $this->hasMany(PqAnswer::class); }
    public function csmsCertificates(): HasMany { return $this->hasMany(CsmsCertificate::class); }
    public function tenders(): HasMany { return $this->hasMany(Tender::class); }
    public function tenderBids(): HasMany { return $this->hasMany(TenderBid::class); }
    public function contracts(): HasMany { return $this->hasMany(Contract::class); }
    public function inductions(): HasMany { return $this->hasMany(Induction::class); }
    public function inductionAttendees(): HasMany { return $this->hasMany(InductionAttendee::class); }
    public function accessCards(): HasMany { return $this->hasMany(AccessCard::class); }
    public function workPermits(): HasMany { return $this->hasMany(WorkPermit::class); }
    public function auditLogs(): HasMany { return $this->hasMany(AuditLog::class); }
}
