<?php

namespace App\Models;

use App\Enums\AreaClass;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;

/** Area di lokasi owner, misalnya Fermentasi, Boiler House, Gudang Amonia, IPAL. */
class Area extends Model
{
    use BelongsToOwner;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['hazard_class' => AreaClass::class];
    }
}
