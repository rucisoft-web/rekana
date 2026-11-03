<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status rencana pekerjaan (M1). */
enum WorkPlanStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Draf = 'draf';
    case Dinilai = 'dinilai';
    case Tender = 'tender';
    case Terkontrak = 'terkontrak';
    case Selesai = 'selesai';

    public const META = [
        'draf' => ['Draf', 'gray'],
        'dinilai' => ['Risiko dinilai', 'info'],
        'tender' => ['Proses seleksi', 'warning'],
        'terkontrak' => ['Terkontrak', 'success'],
        'selesai' => ['Selesai', 'gray'],
    ];
}
