<?php

namespace App\Models\Concerns;

use App\Models\Owner;
use App\Support\CurrentOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Isolasi data per owner.
 *
 * - Global scope membatasi query ke owner aktif (bila ada; portal kontraktor tidak punya owner aktif).
 * - owner_id diisi otomatis saat membuat baris.
 * - Baris anak ditolak bila merujuk induk milik owner lain ($ownerParents: kolom => kelas).
 */
trait BelongsToOwner
{
    public static function bootBelongsToOwner(): void
    {
        static::addGlobalScope('owner', function (Builder $builder) {
            $id = app(CurrentOwner::class)->id();

            if ($id !== null) {
                $builder->where($builder->getModel()->qualifyColumn('owner_id'), $id);
            }
        });

        static::saving(function (Model $model) {
            if (! $model->owner_id) {
                $model->owner_id = app(CurrentOwner::class)->id()
                    ?? throw new LogicException('owner_id wajib diisi untuk '.class_basename($model).'.');
            }

            foreach ($model->ownerParents ?? [] as $column => $class) {
                $parentId = $model->getAttribute($column);

                if ($parentId === null) {
                    continue;
                }

                $parent = $class::withoutGlobalScopes()->whereKey($parentId)->value('owner_id');

                if ((int) $parent !== (int) $model->owner_id) {
                    throw new LogicException(class_basename($model)." tidak boleh merujuk {$column} milik owner lain.");
                }
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }
}
