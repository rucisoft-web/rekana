<?php

namespace App\Filament\App\Resources\Prequalifications;

use App\Enums\PqStatus;
use App\Filament\App\Resources\Prequalifications\Pages\ListPrequalifications;
use App\Filament\App\Resources\Prequalifications\Pages\ScorePrequalification;
use App\Filament\Concerns\OwnerAccess;
use App\Models\Prequalification;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Prakualifikasi (IOGP 423 tahap 2): rekanan menjawab di portal, evaluator menilai, pimpinan menyetujui. */
class PrequalificationResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'prakualifikasi';

    protected static ?string $model = Prequalification::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Prakualifikasi';
    protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'Prakualifikasi';
    protected static ?string $pluralModelLabel = 'Prakualifikasi (PQ)';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return true; }

    public static function getNavigationBadge(): ?string
    {
        $n = Prequalification::whereIn('status', [PqStatus::Diajukan, PqStatus::Dinilai])->count();

        return $n ? (string) $n : null;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('number')->label('Nomor')->searchable(),
            TextColumn::make('contractor.name')->label('Rekanan')->searchable()->weight('bold')->wrap(),
            TextColumn::make('target_risk')->label('Target')->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('score')->label('Skor')->suffix('%')->placeholder('-')->sortable(),
            TextColumn::make('result_risk')->label('Hasil')->badge()->placeholder('-'),
            IconColumn::make('critical_failed')->label('Gagal kritis')->boolean()->trueColor('danger')->falseIcon(''),
            TextColumn::make('submitted_at')->label('Diajukan')->since()->placeholder('-'),
        ])->filters([
            SelectFilter::make('status')->label('Status')->options(PqStatus::class)->multiple(),
        ])->recordActions([EditAction::make()->label('Buka')->icon(Heroicon::OutlinedEye)]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPrequalifications::route('/'), 'edit' => ScorePrequalification::route('/{record}')];
    }
}
