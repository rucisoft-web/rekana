<?php

namespace App\Support;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

/** Akun tamu demo boleh membuka semua formulir dan tombol, tetapi setiap penyimpanan dihentikan dengan pemberitahuan. */
class GuestPreview
{
    protected static bool $notified = false;

    public static function active(): bool
    {
        return (bool) auth()->user()?->isDemoGuest();
    }

    public static function notify(): void
    {
        if (static::$notified) {
            return;
        }
        static::$notified = true;

        Notification::make()->warning()->title('Mode tamu: perubahan tidak disimpan')
            ->body('Anda sedang mencoba Rekana dengan data simulasi. Formulir dan tombol dapat dicoba, tetapi data tidak berubah. Masuk dengan akun sendiri untuk menyimpan.')
            ->send();
    }

    /** @throws Halt */
    public static function block(): never
    {
        static::notify();

        throw (new Halt)->rollBackDatabaseTransaction();
    }
}
