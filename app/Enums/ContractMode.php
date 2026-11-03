<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Mode kontrak IOGP 423. */
enum ContractMode: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Mode1 = 'mode_1';
    case Mode2 = 'mode_2';
    case Mode3 = 'mode_3';

    public const META = [
        'mode_1' => ['Mode 1 (owner mengelola K3)', 'info'],
        'mode_2' => ['Mode 2 (sistem K3 kontraktor, aturan owner)', 'primary'],
        'mode_3' => ['Mode 3 (kontraktor mengelola penuh)', 'warning'],
    ];
}
