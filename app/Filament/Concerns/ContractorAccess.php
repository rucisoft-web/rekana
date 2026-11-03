<?php

namespace App\Filament\Concerns;

use App\Models\Contractor;
use App\Support\GuestPreview;
use Illuminate\Database\Eloquent\Model;

/** Hak akses portal kontraktor: anggota rekanan dapat mengisi data; akun tamu demo dapat mencoba semua formulir tanpa menyimpan. */
trait ContractorAccess
{
    use OwnerAccess {
        rule as protected ownerRule;
    }

    public static function canWrite(): bool
    {
        $tenant = filament()->getTenant();
        if (! $tenant instanceof Contractor) {
            return false;
        }
        if (GuestPreview::active()) {
            return true;
        }

        $pivot = auth()->user()?->contractors()->whereKey($tenant->id)->first()?->pivot;

        return $pivot && $pivot->is_active && $pivot->role !== 'tamu';
    }

    public static function canManage(): bool
    {
        return static::canWrite();
    }

    public static function canCreate(): bool
    {
        return static::canWrite();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canWrite();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canWrite();
    }

    public static function canDeleteAny(): bool
    {
        return static::canWrite();
    }

    public static function rule(callable $fn, string $success): mixed
    {
        return static::ownerRule($fn, $success);
    }
}
