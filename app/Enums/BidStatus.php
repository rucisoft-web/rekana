<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status peserta seleksi. */
enum BidStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Diundang = 'diundang';
    case Diajukan = 'diajukan';
    case Dinilai = 'dinilai';
    case Menang = 'menang';
    case Kalah = 'kalah';

    public const META = [
        'diundang' => ['Diundang', 'gray'],
        'diajukan' => ['HSE Plan diajukan', 'warning'],
        'dinilai' => ['Dinilai', 'info'],
        'menang' => ['Pemenang', 'success'],
        'kalah' => ['Tidak terpilih', 'gray'],
    ];
}
