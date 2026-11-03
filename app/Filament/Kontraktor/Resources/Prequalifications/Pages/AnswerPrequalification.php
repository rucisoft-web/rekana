<?php

namespace App\Filament\Kontraktor\Resources\Prequalifications\Pages;

use App\Filament\Kontraktor\Resources\Prequalifications\PrequalificationResource;
use App\Models\Prequalification;
use App\Models\TemplateItem;
use App\Services\PrequalificationService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

/** Pengisian kuesioner PQ oleh rekanan: jawaban, skor mandiri, dan bukti per butir. */
class AnswerPrequalification extends EditRecord
{
    protected static string $resource = PrequalificationResource::class;

    protected static ?string $title = 'Kuesioner prakualifikasi';

    public function getSubheading(): ?string
    {
        /** @var Prequalification $pq */
        $pq = $this->record;

        return $pq->number.' · '.$pq->owner->name.' · target '.$pq->target_risk->getLabel().' · '.$pq->status->getLabel();
    }

    protected function editable(): bool
    {
        return $this->record->isEditableByContractor() && PrequalificationResource::canWrite();
    }

    public function form(Schema $schema): Schema
    {
        $docs = fn () => $this->record->contractor->contractorDocuments()->pluck('title', 'id');

        return $schema->columns(1)->components([
            Section::make('Permintaan klarifikasi dari owner')->visible(fn () => filled($this->record->clarification_request))->schema([
                Placeholder::make('clar')->hiddenLabel()->content(fn () => $this->record->clarification_request),
            ]),
            Section::make('Petunjuk')->collapsible()->schema([
                Placeholder::make('help')->hiddenLabel()->content(new HtmlString(
                    'Jawab setiap butir dengan jujur dan lampirkan bukti: pilih dokumen yang sudah diunggah di menu <b>Dokumen perusahaan</b>, atau unggah berkas khusus. '
                    .'Skor mandiri 0 sampai 4 membantu evaluator, tetapi skor akhir ditentukan evaluator owner. Butir bertanda <b>KRITIS</b> yang dinilai 0 membuat PQ gagal.'
                )),
            ]),
            Repeater::make('answers')->label('Butir kuesioner')->relationship('answers', fn ($q) => $q->with('item')->orderBy('id'))
                ->addable(false)->deletable(false)->reorderable(false)->collapsible()
                ->itemLabel(fn (array $state) => ($i = TemplateItem::find($state['template_item_id'] ?? null)) ? $i->code.' · '.$i->question.($i->is_critical ? '  [KRITIS]' : '') : null)
                ->schema([
                    Grid::make(4)->schema([
                        Placeholder::make('hint')->label('Bukti yang diharapkan')->columnSpan(4)->content(fn ($record) => $record?->item?->evidence_hint),
                        Textarea::make('answer')->label('Jawaban')->rows(2)->columnSpan(3)->disabled(fn () => ! $this->editable()),
                        Select::make('self_score')->label('Skor mandiri')->options([0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4'])->disabled(fn () => ! $this->editable()),
                        Select::make('contractor_document_id')->label('Dokumen pendukung')->options($docs)->columnSpan(2)->disabled(fn () => ! $this->editable()),
                        FileUpload::make('evidence_path')->label('Atau unggah bukti')->disk('local')->directory('bukti-pq')->columnSpan(2)->disabled(fn () => ! $this->editable()),
                    ]),
                ]),
        ]);
    }

    protected function getFormActions(): array
    {
        return $this->editable() ? [$this->getSaveFormAction()->label('Simpan draf')] : [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')->label('Ajukan ke owner')->icon(Heroicon::OutlinedPaperAirplane)->color('success')
                ->visible(fn () => $this->editable())->requiresConfirmation()
                ->modalDescription('Setelah diajukan, jawaban tidak dapat diubah kecuali owner meminta klarifikasi.')
                ->action(function () {
                    $this->save(false);
                    PrequalificationResource::rule(fn () => app(PrequalificationService::class)->submit($this->record->fresh()), 'PQ diajukan ke owner');
                }),
        ];
    }
}
