<?php

namespace App\Support;

use App\Models\Owner;

/**
 * Owner aktif untuk permintaan atau perintah yang sedang berjalan.
 *
 * Diisi middleware tenant panel owner dan perintah artisan, lalu dipakai global scope BelongsToOwner.
 */
class CurrentOwner
{
    protected ?Owner $owner = null;

    public function set(?Owner $owner): void
    {
        $this->owner = $owner;
    }

    public function get(): ?Owner
    {
        return $this->owner;
    }

    public function id(): ?int
    {
        return $this->owner?->getKey();
    }

    public function runAs(Owner $owner, callable $callback): mixed
    {
        $previous = $this->owner;
        $this->owner = $owner;

        try {
            return $callback();
        } finally {
            $this->owner = $previous;
        }
    }
}
