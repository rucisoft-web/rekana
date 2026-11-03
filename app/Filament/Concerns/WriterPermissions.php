<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/** Data kerja: semua anggota melihat, peran penulis mencatat, Manajer/Admin menghapus. */
trait WriterPermissions
{
    use InteractsWithTenant;

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
        return static::canManage();
    }

    public static function canDeleteAny(): bool
    {
        return static::canManage();
    }
}
