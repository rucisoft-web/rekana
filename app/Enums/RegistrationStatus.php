<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status rekanan di satu owner. */
enum RegistrationStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Menunggu = 'menunggu';
    case Aktif = 'aktif';
    case Ditangguhkan = 'ditangguhkan';
    case DaftarHitam = 'daftar_hitam';

    public const META = [
        'menunggu' => ['Menunggu verifikasi', 'warning'],
        'aktif' => ['Aktif', 'success'],
        'ditangguhkan' => ['Ditangguhkan', 'gray'],
        'daftar_hitam' => ['Daftar hitam', 'danger'],
    ];
}
