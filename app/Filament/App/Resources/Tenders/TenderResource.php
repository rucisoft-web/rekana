<?php

namespace App\Filament\App\Resources\Tenders;

use App\Enums\TenderStatus;
use App\Filament\App\Resources\Tenders\Pages\ListTenders;
use App\Filament\App\Resources\Tenders\Pages\ViewTender;
use App\Filament\App\Resources\Tenders\RelationManagers\BidsRelationManager;
use App\Filament\Concerns\OwnerAccess;
use App\Models\Tender;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Seleksi (IOGP 423 tahap 3): hanya rekanan bersertifikat yang mencakup kategori risiko yang dapat diundang. */
class TenderResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'seleksi';

    protected static ?string $model = Tender::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;
    protected static string|UnitEnum|null $navigationGroup = 'Seleksi';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Seleksi';
    protected static ?string $pluralModelLabel = 'Seleksi rekanan';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(4)->schema([
                TextEntry::make('number')->label('Nomor'),
                TextEntry::make('workPlan.title')->label('Pekerjaan')->columnSpan(2),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('workPlan.risk_category')->label('Kategori risiko')->badge(),
                TextEntry::make('closes_at')->label('Batas HSE Plan')->dateTime('d M Y H:i'),
                TextEntry::make('hse_pass_score')->label('Ambang lulus HSE')->suffix('%'),
                TextEntry::make('hse_weight')->label('Bobot HSE')->suffix('%'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('number')->label('Nomor')->searchable(),
            TextColumn::make('title')->label('Seleksi')->weight('bold')->wrap()->searchable(),
            TextColumn::make('workPlan.risk_category')->label('Risiko')->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('bids_count')->label('Peserta')->counts('bids'),
            TextColumn::make('closes_at')->label('Batas')->dateTime('d M Y'),
        ])->recordActions([ViewAction::make()->label('Buka')]);
    }

    public static function getRelations(): array
    {
        return [BidsRelationManager::class];
    }

    public static function getPages(): array
    {
        return ['index' => ListTenders::route('/'), 'view' => ViewTender::route('/{record}')];
    }
}
