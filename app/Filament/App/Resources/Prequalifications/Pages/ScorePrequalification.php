<?php

namespace App\Filament\App\Resources\Prequalifications\Pages;

use App\Enums\PqStatus;
use App\Filament\App\Resources\Prequalifications\PrequalificationResource;
use App\Models\Prequalification;
use App\Services\PrequalificationService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
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

/**
 * Halaman penilaian PQ. Evaluator memberi skor 0-4 untuk setiap butir berdasarkan bukti, lalu menghitung skor;
 * pimpinan (orang yang berbeda) menyetujui dan sertifikat CSMS terbit otomatis.
 */
class ScorePrequalification extends EditRecord
{
    protected static string $resource = PrequalificationResource::class;

    protected static ?string $title = 'Penilaian prakualifikasi';

    public function getSubheading(): ?string
    {
        /** @var Prequalification $pq */
        $pq = $this->record;

        return $pq->number.' · '.$pq->contractor->name.' · target '.$pq->target_risk->getLabel().' · '.$pq->status->getLabel()
            .($pq->score !== null ? ' · skor '.$pq->score.'%' : '');
    }

    protected function canScore(): bool
    {
        return in_array($this->record->status, [PqStatus::Diajukan, PqStatus::Dinilai], true) && (bool) PrequalificationResource::role()?->canEvaluate();
    }

    public function form(Schema $schema): Schema
    {
        $scale = [0 => '0 · tidak ada', 1 => '1 · lemah', 2 => '2 · sebagian', 3 => '3 · memadai', 4 => '4 · baik sekali'];

        return $schema->columns(1)->components([
            Section::make('Cara menilai')->collapsible()->schema([
                Placeholder::make('help')->hiddenLabel()->content(new HtmlString(
                    'Skor PQ = Σ(bobot × skor) ÷ Σ(bobot × 4) × 100. Butir <b>kritis</b> yang diberi skor 0 membuat PQ gagal apa pun total skornya. '
                    .'Ambang lulus: Rendah '.implode(' · ', array_map(fn ($k, $v) => ucfirst($k).' '.$v.'%', array_keys(filament()->getTenant()->pqThresholds()), filament()->getTenant()->pqThresholds())).'. '
                    .'Penilai tidak boleh menyetujui penilaiannya sendiri (prinsip empat mata).'
                )),
            ]),
            Section::make('Kunjungan lapangan')->columns(2)->visible(fn () => $this->record->target_risk->value === 'tinggi')->schema([
                DatePicker::make('site_visit_date')->label('Tanggal kunjungan')->disabled(fn () => ! $this->canScore()),
                Textarea::make('site_visit_notes')->label('Catatan kunjungan')->disabled(fn () => ! $this->canScore()),
            ]),
            Repeater::make('answers')->label('Jawaban dan penilaian')->relationship('answers', fn ($query) => $query->with('item')->orderBy('id'))
                ->addable(false)->deletable(false)->reorderable(false)
                ->itemLabel(fn (array $state) => ($item = \App\Models\TemplateItem::find($state['template_item_id'] ?? null)) ? $item->code.' · '.$item->question.($item->is_critical ? '  [KRITIS]' : '').' · bobot '.$item->weight : null)
                ->schema([
                    Grid::make(4)->schema([
                        Placeholder::make('answer_text')->label('Jawaban rekanan')->columnSpan(2)->content(fn ($record) => $record?->answer ?: '-'),
                        Placeholder::make('evidence')->label('Bukti yang diharapkan')->content(fn ($record) => $record?->item?->evidence_hint),
                        Placeholder::make('self')->label('Skor mandiri rekanan')->content(fn ($record) => $record?->self_score ?? '-'),
                        Select::make('score')->label('Skor evaluator')->options($scale)->disabled(fn () => ! $this->canScore()),
                        Textarea::make('evaluator_note')->label('Catatan evaluator')->rows(1)->columnSpan(3)->disabled(fn () => ! $this->canScore()),
                    ]),
                ]),
        ]);
    }

    protected function getFormActions(): array
    {
        return $this->canScore() ? [$this->getSaveFormAction()->label('Simpan skor')] : [];
    }

    protected function getHeaderActions(): array
    {
        $svc = app(PrequalificationService::class);
        $r = fn () => PrequalificationResource::role();

        return [
            Action::make('evaluate')->label('Hitung skor dan kirim ke persetujuan')->icon(Heroicon::OutlinedCalculator)
                ->visible(fn () => $this->canScore() && $this->record->status === PqStatus::Diajukan)
                ->schema([Textarea::make('notes')->label('Ringkasan penilaian')])
                ->action(function (array $data) use ($svc) {
                    $this->save(false);
                    PrequalificationResource::rule(fn () => $svc->evaluate($this->record->fresh(), auth()->user(), $data['notes'] ?? null), 'PQ dinilai, menunggu persetujuan');
                    $this->refreshFormData(['status', 'score']);
                }),
            Action::make('clarify')->label('Minta klarifikasi')->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)->color('warning')
                ->visible(fn () => $this->canScore())
                ->schema([Textarea::make('message')->label('Hal yang perlu dilengkapi rekanan')->required()])
                ->action(fn (array $data) => PrequalificationResource::rule(fn () => $svc->requestClarification($this->record, $data['message']), 'Permintaan klarifikasi dikirim')),
            Action::make('approve')->label('Setujui dan terbitkan sertifikat')->icon(Heroicon::OutlinedCheckBadge)->color('success')
                ->visible(fn () => $this->record->status === PqStatus::Dinilai && $r()?->canApprove())
                ->requiresConfirmation()->modalDescription(fn () => 'Skor '.$this->record->score.'%. Hasil: '.($this->record->critical_failed ? 'gagal butir kritis' : ($this->record->result_risk?->getLabel() ?? 'tidak lulus')).'.')
                ->action(fn () => PrequalificationResource::rule(fn () => $svc->approve($this->record, auth()->user()), 'Keputusan PQ disimpan')),
        ];
    }
}
