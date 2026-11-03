<?php

namespace App\Filament\Concerns;

use App\Enums\Role;
use App\Models\AccreditationProject;
use App\Models\Institution;
use App\Models\Membership;
use App\Models\User;
use Filament\Facades\Filament;

/** Pintasan untuk resource dan halaman: pengguna, institusi aktif, peran, dan proyek berjalan. */
trait InteractsWithTenant
{
    protected static function tenantUser(): ?User
    {
        /** @var User|null */
        return auth()->user();
    }

    protected static function tenantInstitution(): ?Institution
    {
        /** @var Institution|null */
        return Filament::getTenant();
    }

    protected static function tenantRole(): ?Role
    {
        return static::tenantUser()?->roleIn(static::tenantInstitution());
    }

    protected static function tenantMembership(): ?Membership
    {
        return static::tenantUser()?->activeMembership(static::tenantInstitution());
    }

    protected static function canWrite(): bool
    {
        return (bool) static::tenantRole()?->canWrite();
    }

    protected static function canManage(): bool
    {
        return (bool) static::tenantRole()?->canManage();
    }

    protected static function canReview(): bool
    {
        return (bool) static::tenantRole()?->canReview();
    }

    protected static function canAudit(): bool
    {
        return (bool) static::tenantRole()?->canAudit();
    }

    protected static function canLead(): bool
    {
        return (bool) static::tenantRole()?->canLead();
    }

    protected static function activeProject(): ?AccreditationProject
    {
        return static::tenantInstitution()?->activeProject();
    }

    /** @return array<int, string> anggota aktif institusi ini. */
    protected static function memberOptions(): array
    {
        return Membership::query()->with('user')->where('is_active', true)->get()
            ->sortBy('user.name')
            ->mapWithKeys(fn (Membership $m) => [$m->user_id => $m->user->name])
            ->all();
    }
}
