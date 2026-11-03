<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use SensitiveParameter;

/**
 * Satu akun dapat menjadi anggota owner (panel /app) dan/atau pengguna rekanan (panel /kontraktor).
 */
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const DEMO_GUEST_EMAIL = 'tamu@demo.rekana.test';

    protected $fillable = ['name', 'email', 'password', 'is_active', 'is_platform_admin'];

    protected $attributes = ['is_active' => true, 'is_platform_admin' => false];

    protected $hidden = ['password', 'remember_token', 'app_authentication_secret', 'app_authentication_recovery_codes'];

    /** @var array<int, Membership|null> */
    protected array $membershipCache = [];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_platform_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class)->withoutGlobalScopes();
    }

    public function contractors(): BelongsToMany
    {
        return $this->belongsToMany(Contractor::class, 'contractor_users')->withPivot(['role', 'is_active'])->withTimestamps();
    }

    public function activeMembership(Owner|int|null $owner): ?Membership
    {
        $id = $owner instanceof Owner ? $owner->id : $owner;

        if ($id === null) {
            return null;
        }

        if (! array_key_exists($id, $this->membershipCache)) {
            $this->membershipCache[$id] = Membership::withoutGlobalScopes()
                ->where('owner_id', $id)->where('user_id', $this->id)->where('is_active', true)->first();
        }

        return $this->membershipCache[$id];
    }

    public function roleIn(Owner|int|null $owner): ?Role
    {
        return $this->activeMembership($owner)?->role;
    }

    public function isContractorMember(Contractor|int $contractor): bool
    {
        $id = $contractor instanceof Contractor ? $contractor->id : $contractor;

        return $this->contractors()->where('contractors.id', $id)->wherePivot('is_active', true)->exists();
    }

    public function isDemoGuest(): bool
    {
        return $this->email === self::DEMO_GUEST_EMAIL;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'platform' => $this->is_platform_admin,
            default => true,
        };
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($panel->getId() === 'kontraktor') {
            return $this->contractors()->wherePivot('is_active', true)->where('contractors.is_active', true)->orderBy('name')->get();
        }

        $ids = $this->memberships()->where('is_active', true)->pluck('owner_id');

        return Owner::query()->whereIn('id', $ids)->where('is_active', true)->orderBy('name')->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $this->is_active || ! $tenant->is_active) {
            return false;
        }

        return $tenant instanceof Contractor
            ? $this->isContractorMember($tenant)
            : $this->activeMembership($tenant) !== null;
    }

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(#[SensitiveParameter] ?string $secret): void
    {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->app_authentication_recovery_codes;
    }

    public function saveAppAuthenticationRecoveryCodes(#[SensitiveParameter] ?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
