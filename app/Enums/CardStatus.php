<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status kartu akses pekerja. */
enum CardStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Aktif = 'aktif';
    case Diblokir = 'diblokir';
    case Kedaluwarsa = 'kedaluwarsa';
    case Dicabut = 'dicabut';

    public const META = [
        'aktif' => ['Aktif', 'success'],
        'diblokir' => ['Diblokir', 'danger'],
        'kedaluwarsa' => ['Kedaluwarsa', 'gray'],
        'dicabut' => ['Dicabut', 'gray'],
    ];
}
