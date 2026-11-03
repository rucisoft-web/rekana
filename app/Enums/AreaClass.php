<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Kelas area di lokasi owner. */
enum AreaClass: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Umum = 'umum';
    case ProduksiPangan = 'produksi_pangan';
    case Terbatas = 'terbatas';
    case Bahaya = 'bahaya';

    public const META = [
        'umum' => ['Umum', 'gray'],
        'produksi_pangan' => ['Produksi pangan (GMP dan halal)', 'info'],
        'terbatas' => ['Terbatas', 'warning'],
        'bahaya' => ['Bahaya', 'danger'],
    ];
}
