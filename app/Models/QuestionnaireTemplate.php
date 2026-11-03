<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Template kuesioner prakualifikasi. owner_id kosong = template baku (ISO 45001 dan SMK3) yang dapat
 * dipakai semua owner; owner dapat menyalin lalu menyesuaikannya.
 */
class QuestionnaireTemplate extends Model
{
    protected $attributes = ['is_active' => true];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(TemplateElement::class)->orderBy('sort');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TemplateItem::class)->orderBy('sort');
    }

    /** Template yang boleh dipakai owner: miliknya sendiri dan template baku. */
    public function scopeAvailableTo(Builder $query, Owner|int|null $owner): Builder
    {
        $id = $owner instanceof Owner ? $owner->id : $owner;

        return $query->where('is_active', true)->where(fn ($q) => $q->whereNull('owner_id')->orWhere('owner_id', $id));
    }

    /** Salinan template untuk owner, lengkap dengan elemen dan butirnya. */
    public function copyFor(Owner $owner): self
    {
        $copy = $this->replicate(['owner_id']);
        $copy->owner_id = $owner->id;
        $copy->code = $this->code.'-'.$owner->slug;
        $copy->name = $this->name.' ('.$owner->name.')';
        $copy->save();

        foreach ($this->elements()->with('items')->get() as $element) {
            $newElement = $copy->elements()->create($element->only(['code', 'name', 'sort']));
            foreach ($element->items as $item) {
                $newElement->items()->create($item->only(['code', 'question', 'guidance', 'evidence_hint', 'weight', 'is_critical', 'min_risk', 'sort'])
                    + ['questionnaire_template_id' => $copy->id]);
            }
        }

        return $copy;
    }
}
