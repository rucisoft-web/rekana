<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Jawaban rekanan untuk satu butir: uraian, bukti, skor mandiri, lalu skor evaluator. */
class PqAnswer extends Model
{
    use Auditable, BelongsToOwner;

    protected $guarded = ['id'];

    protected array $tracked = ['score'];

    protected array $ownerParents = ['prequalification_id' => Prequalification::class];

    public function prequalification(): BelongsTo { return $this->belongsTo(Prequalification::class); }
    public function item(): BelongsTo { return $this->belongsTo(TemplateItem::class, 'template_item_id'); }
    public function document(): BelongsTo { return $this->belongsTo(ContractorDocument::class, 'contractor_document_id'); }

    public function auditLabel(): string
    {
        return ($this->prequalification?->number ?? 'PQ').' butir '.$this->item?->code;
    }
}
