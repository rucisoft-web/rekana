<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status kontrak. */
enum ContractStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Mobilisasi = 'mobilisasi';
    case Berjalan = 'berjalan';
    case Dihentikan = 'dihentikan';
    case Selesai = 'selesai';

    public const META = [
        'mobilisasi' => ['Mobilisasi', 'warning'],
        'berjalan' => ['Berjalan', 'success'],
        'dihentikan' => ['Dihentikan sementara', 'danger'],
        'selesai' => ['Selesai', 'gray'],
    ];
}
