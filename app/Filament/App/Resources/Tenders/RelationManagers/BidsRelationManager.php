<?php

namespace App\Filament\App\Resources\Tenders\RelationManagers;

use App\Enums\BidStatus;
use App\Filament\App\Resources\Tenders\TenderResource;
use App\Models\TenderBid;
use App\Services\TenderService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Peserta seleksi: HSE Plan dinilai dengan rubrik enam kriteria, lalu pemenang ditetapkan dan kontrak dibuat. */
class BidsRelationManager extends RelationManager
{
    protected static string $relationship = 'bids';

    protected static ?string $title = 'Peserta dan penilaian HSE Plan';

    public function table(Table $table): Table
    {
        $scale = [0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4'];
        $svc = fn () => app(TenderService::class);

        return $table->columns([
            TextColumn::make('contractor.name')->label('Rekanan')->weight('bold')->wrap(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('hse_plan_summary')->label('Ringkasan HSE Plan')->wrap()->limit(120)->placeholder('Belum diunggah'),
            TextColumn::make('hse_score')->label('Nilai HSE')->suffix('%')->placeholder('-'),
            IconColumn::make('hse_passed')->label('Lulus HSE')->boolean(),
            TextColumn::make('technical_score')->label('Teknis')->placeholder('-'),
            TextColumn::make('combined')->label('Nilai akhir')->state(fn (TenderBid $b) => $b->hse_score !== null ? $svc()->combinedScore($b) : null)->placeholder('-'),
        ])->recordActions([
            Action::make('evaluate')->label('Nilai HSE Plan')->icon(Heroicon::OutlinedCalculator)
                ->visible(fn (TenderBid $b) => $b->status === BidStatus::Diajukan && TenderResource::role()?->canEvaluate())
                ->schema(array_merge(
                    collect(config('rekana.hse_plan_rubric'))->map(fn ($label, $key) => Select::make("scores.$key")->label($label)->options($scale)->required())->values()->all(),
                    [TextInput::make('technical')->label('Nilai teknis (0-100)')->numeric(), TextInput::make('price')->label('Harga penawaran (Rp)')->numeric(), Textarea::make('notes')->label('Catatan')],
                ))
                ->action(fn (TenderBid $b, array $data) => TenderResource::rule(fn () => $svc()->evaluateBid($b, $data['scores'], auth()->user(), $data['technical'] ?? null, $data['price'] ?? null, $data['notes'] ?? null), 'HSE Plan dinilai')),
            Action::make('award')->label('Tetapkan pemenang')->icon(Heroicon::OutlinedTrophy)->color('success')
                ->visible(fn (TenderBid $b) => $b->status === BidStatus::Dinilai && TenderResource::role()?->canApprove())
                ->schema([
                    TextInput::make('number')->label('Nomor kontrak')->required(),
                    DatePicker::make('start_date')->label('Mulai')->required(),
                    DatePicker::make('end_date')->label('Selesai')->required(),
                    TextInput::make('value')->label('Nilai kontrak (Rp)')->numeric(),
                ])
                ->action(fn (TenderBid $b, array $data) => TenderResource::rule(fn () => $svc()->award($b, $data + ['title' => $b->tender->title, 'hse_officer_id' => auth()->id()]), 'Pemenang ditetapkan; kontrak dibuat di menu Kontrak')),
        ]);
    }
}
