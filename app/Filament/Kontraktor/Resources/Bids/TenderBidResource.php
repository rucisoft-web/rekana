<?php

namespace App\Filament\Kontraktor\Resources\Bids;

use App\Enums\BidStatus;
use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Bids\Pages\ManageTenderBids;
use App\Models\TenderBid;
use App\Services\TenderService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Undangan seleksi dari owner; rekanan mengunggah HSE Plan sebelum batas waktu. */
class TenderBidResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'undangan-seleksi';
    protected static ?string $model = TenderBid::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;
    protected static string|UnitEnum|null $navigationGroup = 'Pekerjaan';
    protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'Undangan seleksi';
    protected static ?string $pluralModelLabel = 'Undangan seleksi';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('tender.title')->label('Pekerjaan')->weight('bold')->wrap()->description(fn (TenderBid $b) => $b->tender->owner->name),
            TextColumn::make('tender.workPlan.risk_category')->label('Risiko')->badge(),
            TextColumn::make('tender.closes_at')->label('Batas')->dateTime('d M Y H:i'),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('hse_score')->label('Nilai HSE')->suffix('%')->placeholder('-'),
        ])->recordActions([
            Action::make('submit')->label('Unggah HSE Plan')->icon(Heroicon::OutlinedArrowUpTray)
                ->visible(fn (TenderBid $b) => static::canWrite() && in_array($b->status, [BidStatus::Diundang, BidStatus::Diajukan], true))
                ->schema([
                    FileUpload::make('path')->label('Berkas HSE Plan (PDF)')->disk('local')->directory('hse-plan')->acceptedFileTypes(['application/pdf']),
                    Textarea::make('summary')->label('Ringkasan')->rows(4)->helperText('Organisasi K3 proyek, JSA utama, personel bersertifikat, peralatan, tanggap darurat.'),
                ])
                ->action(fn (TenderBid $b, array $data) => static::rule(fn () => app(TenderService::class)->submitHsePlan($b, $data['path'] ?? null, $data['summary'] ?? null), 'HSE Plan terkirim')),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTenderBids::route('/')];
    }
}
