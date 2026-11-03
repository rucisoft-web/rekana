<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Butir pertanyaan berbobot. Butir kritis bernilai 0 menggagalkan prakualifikasi. */
class TemplateItem extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_critical' => 'boolean', 'min_risk' => RiskLevel::class];
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(TemplateElement::class, 'template_element_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireTemplate::class, 'questionnaire_template_id');
    }

    /** Butir berlaku untuk PQ dengan target risiko $target bila min_risk-nya tercakup target. */
    public function appliesTo(RiskLevel $target): bool
    {
        return $target->covers($this->min_risk);
    }
}
