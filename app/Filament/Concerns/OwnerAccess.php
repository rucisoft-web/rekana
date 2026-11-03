<?php

namespace App\Filament\Concerns;

use App\Enums\Role;
use App\Support\GuestPreview;
use App\Support\RuleViolation;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

/**
 * Hak akses berbasis peran di panel owner, dan pembungkus aksi aturan bisnis: pelanggaran aturan
 * (RuleViolation) ditampilkan sebagai notifikasi berisi alasan, bukan halaman galat.
 */
trait OwnerAccess
{
    public static function role(): ?Role
    {
        // Tamu demo melihat tampilan Administrator agar semua tombol dapat dicoba (penyimpanan tetap dihentikan).
        return GuestPreview::active() ? Role::Admin : auth()->user()?->roleIn(filament()->getTenant());
    }

    public static function canWrite(): bool
    {
        return GuestPreview::active() || (bool) static::role()?->canWrite();
    }

    public static function canManage(): bool
    {
        return GuestPreview::active() || (bool) static::role()?->canManage();
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
        return static::canManage();
    }

    public static function canDeleteAny(): bool
    {
        return static::canManage();
    }

    /** Jalankan aksi layanan; tampilkan alasan bila aturan dilanggar. */
    public static function rule(callable $fn, string $success): mixed
    {
        if (GuestPreview::active()) {
            GuestPreview::notify();

            return null;
        }

        try {
            $result = $fn();
            Notification::make()->success()->title($success)->send();

            return $result;
        } catch (RuleViolation $e) {
            Notification::make()->danger()->title('Belum dapat diproses')->body(implode("\n", $e->reasons))->persistent()->send();
        } catch (\LogicException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }

        return null;
    }
}
