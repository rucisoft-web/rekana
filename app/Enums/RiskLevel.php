<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Kategori risiko pekerjaan (PTK 005 dan pedoman CSMS umumnya memakai tiga tingkat). */
enum RiskLevel: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Rendah = 'rendah';
    case Menengah = 'menengah';
    case Tinggi = 'tinggi';

    public const META = [
        'rendah' => ['Rendah', 'success'],
        'menengah' => ['Menengah', 'warning'],
        'tinggi' => ['Tinggi', 'danger'],
    ];

    public function rank(): int
    {
        return match ($this) {
            self::Rendah => 1,
            self::Menengah => 2,
            self::Tinggi => 3,
        };
    }

    /** Apakah kategori ini mencakup pekerjaan dengan kategori $other (Tinggi mencakup Menengah dan Rendah). */
    public function covers(self $other): bool
    {
        return $this->rank() >= $other->rank();
    }

    public static function fromScore(int $score): self
    {
        return match (true) {
            $score >= (int) config('rekana.risk.high_from') => self::Tinggi,
            $score >= (int) config('rekana.risk.medium_from') => self::Menengah,
            default => self::Rendah,
        };
    }
}
