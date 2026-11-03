<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/** Data konfigurasi: semua anggota boleh melihat, hanya Manajer/Admin yang mengubah. */
trait ManagerPermissions
{
    use InteractsWithTenant;

    public static function canCreate(): bool
    {
        return static::canManage();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canManage();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canManage();
    }

    public static function canDeleteAny(): bool
    {
        return static::canManage();
    }
}
