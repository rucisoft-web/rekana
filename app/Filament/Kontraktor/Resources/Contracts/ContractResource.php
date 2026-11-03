<?php

namespace App\Filament\Kontraktor\Resources\Contracts;

use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Contracts\Pages\ManageContracts;
use App\Models\Contract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Kontrak rekanan di semua owner (hanya lihat). */
class ContractResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'kontrak';
    protected static ?string $model = Contract::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Pekerjaan';
    protected static ?int $navigationSort = 30;
    protected static ?string $modelLabel = 'Kontrak';
    protected static ?string $pluralModelLabel = 'Kontrak';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('start_date', 'desc')->columns([
            TextColumn::make('number')->label('Nomor'),
            TextColumn::make('title')->label('Pekerjaan')->weight('bold')->wrap()->description(fn (Contract $c) => $c->owner->name),
            TextColumn::make('workPlan.risk_category')->label('Risiko')->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
            TextColumn::make('end_date')->label('Selesai')->date('d M Y'),
            TextColumn::make('bridging_document')->label('Bridging document')->wrap()->limit(80)->placeholder('-')->toggleable(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContracts::route('/')];
    }
}
