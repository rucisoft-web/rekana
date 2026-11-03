<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Status prakualifikasi. */
enum PqStatus: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Draf = 'draf';
    case Diajukan = 'diajukan';
    case Klarifikasi = 'klarifikasi';
    case Dinilai = 'dinilai';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public const META = [
        'draf' => ['Draf (diisi rekanan)', 'gray'],
        'diajukan' => ['Diajukan, menunggu penilaian', 'warning'],
        'klarifikasi' => ['Perlu klarifikasi rekanan', 'warning'],
        'dinilai' => ['Dinilai, menunggu persetujuan', 'info'],
        'disetujui' => ['Disetujui', 'success'],
        'ditolak' => ['Tidak lulus', 'danger'],
    ];
}
