<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateElement extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireTemplate::class, 'questionnaire_template_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TemplateItem::class)->orderBy('sort');
    }
}
