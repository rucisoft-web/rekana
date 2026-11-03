<?php

namespace App\Filament\Kontraktor\Resources\Statistics;

use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Statistics\Pages\ManageSafetyStatistics;
use App\Models\SafetyStatistic;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Statistik K3 tahunan; LTIFR dan TRIR dihitung otomatis dan dilihat semua owner. */
class SafetyStatisticResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'statistik-k3';
    protected static ?string $model = SafetyStatistic::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Perusahaan';
    protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'Statistik K3';
    protected static ?string $pluralModelLabel = 'Statistik K3 tahunan';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('year')->label('Tahun')->numeric()->required()->default(now()->year - 1),
            TextInput::make('manhours')->label('Total jam kerja')->numeric()->required()->helperText('Jumlah pekerja × jam kerja setahun, termasuk lembur.'),
            TextInput::make('fatalities')->label('Kecelakaan fatal')->numeric()->default(0),
            TextInput::make('lti_cases')->label('Kasus hilang waktu (LTI)')->numeric()->default(0)->helperText('Cedera yang membuat pekerja tidak dapat bekerja pada shift berikutnya.'),
            TextInput::make('recordable_cases')->label('Kasus tercatat (TRI)')->numeric()->default(0)->helperText('Fatal + LTI + kerja terbatas + perawatan medis.'),
            TextInput::make('lost_days')->label('Hari hilang')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('year', 'desc')->columns([
            TextColumn::make('year')->label('Tahun')->weight('bold'),
            TextColumn::make('manhours')->label('Jam kerja')->numeric(thousandsSeparator: '.'),
            TextColumn::make('fatalities')->label('Fatal'),
            TextColumn::make('lti_cases')->label('LTI'),
            TextColumn::make('recordable_cases')->label('TRI'),
            TextColumn::make('ltifr')->label('LTIFR')->state(fn (SafetyStatistic $s) => $s->ltifr()),
            TextColumn::make('trir')->label('TRIR')->state(fn (SafetyStatistic $s) => $s->trir()),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSafetyStatistics::route('/')];
    }
}
