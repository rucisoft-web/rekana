<?php

namespace App\Enums\Concerns;

/**
 * Label dan warna enum dari konstanta META: nilai => [label, warna Filament].
 */
trait EnumMeta
{
    use HasOptions;

    public function getLabel(): string
    {
        return self::META[$this->value][0];
    }

    public function getColor(): string
    {
        return self::META[$this->value][1] ?? 'gray';
    }

    public static function labelOf(?string $value): string
    {
        return $value === null ? '-' : (self::tryFrom($value)?->getLabel() ?? $value);
    }
}
