<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status seleksi kontraktor (M3). */
enum TenderStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Draf = 'draf';
    case Dibuka = 'dibuka';
    case Evaluasi = 'evaluasi';
    case Selesai = 'selesai';
    case Batal = 'batal';

    public const META = [
        'draf' => ['Draf', 'gray'],
        'dibuka' => ['Dibuka', 'warning'],
        'evaluasi' => ['Evaluasi', 'info'],
        'selesai' => ['Pemenang ditetapkan', 'success'],
        'batal' => ['Batal', 'danger'],
    ];
}
