<?php

namespace App\Filament\Kontraktor\Resources\Prequalifications;

use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Prequalifications\Pages\AnswerPrequalification;
use App\Filament\Kontraktor\Resources\Prequalifications\Pages\ListPrequalifications;
use App\Models\Prequalification;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Kuesioner prakualifikasi dari setiap owner: dijawab, dilampiri bukti, lalu diajukan. */
class PrequalificationResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'prakualifikasi';
    protected static ?string $model = Prequalification::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Pekerjaan';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Prakualifikasi';
    protected static ?string $pluralModelLabel = 'Prakualifikasi (PQ)';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return true; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('number')->label('Nomor'),
            TextColumn::make('owner.name')->label('Owner')->weight('bold')->wrap(),
            TextColumn::make('target_risk')->label('Target')->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('score')->label('Skor')->suffix('%')->placeholder('-'),
            TextColumn::make('result_risk')->label('Hasil')->badge()->placeholder('-'),
            TextColumn::make('clarification_request')->label('Permintaan klarifikasi')->wrap()->limit(100)->placeholder('-'),
        ])->recordActions([EditAction::make()->label(fn (Prequalification $pq) => $pq->isEditableByContractor() ? 'Isi kuesioner' : 'Lihat')]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPrequalifications::route('/'), 'edit' => AnswerPrequalification::route('/{record}')];
    }
}
