<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status izin kerja. */
enum PermitStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Draf = 'draf';
    case Diajukan = 'diajukan';
    case Disetujui = 'disetujui';
    case Aktif = 'aktif';
    case Ditutup = 'ditutup';
    case Dibatalkan = 'dibatalkan';

    public const META = [
        'draf' => ['Draf', 'gray'],
        'diajukan' => ['Diajukan', 'warning'],
        'disetujui' => ['Disetujui', 'info'],
        'aktif' => ['Aktif', 'success'],
        'ditutup' => ['Ditutup', 'gray'],
        'dibatalkan' => ['Dibatalkan', 'danger'],
    ];
}
