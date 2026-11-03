<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** M4: sesi induksi K3, keamanan pangan, dan halal untuk pekerja rekanan. */
class Induction extends Model
{
    use BelongsToOwner;

    protected $guarded = ['id'];

    protected array $ownerParents = ['contract_id' => Contract::class, 'area_id' => Area::class];

    protected function casts(): array
    {
        return ['held_at' => 'date', 'topics' => 'array'];
    }

    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function attendees(): HasMany { return $this->hasMany(InductionAttendee::class); }
}
