<?php

namespace App\Support;

use RuntimeException;

/** Pelanggaran aturan bisnis CSMS; pesan-pesannya ditampilkan apa adanya kepada pengguna. */
class RuleViolation extends RuntimeException
{
    /** @param array<int, string> $reasons */
    public function __construct(public readonly array $reasons)
    {
        parent::__construct(implode(' ', $reasons));
    }

    /** @param array<int, string> $reasons */
    public static function throwIf(array $reasons): void
    {
        $reasons = array_values(array_filter($reasons));

        if ($reasons !== []) {
            throw new self($reasons);
        }
    }
}
