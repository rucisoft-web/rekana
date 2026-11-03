<?php

namespace App\Filament\App\Resources\Areas;

use App\Enums\AreaClass;
use App\Filament\App\Resources\Areas\Pages\ManageAreas;
use App\Filament\Concerns\OwnerAccess;
use App\Models\Area;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Area kerja beserta kelas bahayanya; dipakai penilaian risiko, induksi, dan izin kerja. */
class AreaResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'area';

    protected static ?string $model = Area::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Area kerja';
    protected static ?string $pluralModelLabel = 'Area kerja';

    public static function canCreate(): bool { return static::canManage(); }
    public static function canEdit(Model $record): bool { return static::canManage(); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label('Kode')->required()->maxLength(10),
            TextInput::make('name')->label('Nama area')->required(),
            Select::make('hazard_class')->label('Kelas area')->options(AreaClass::class)->required()->default('umum')
                ->helperText('Area Bahaya mewajibkan uji gas (LEL) untuk kerja panas; Produksi pangan memicu aturan GMP dan halal.'),
            Textarea::make('description')->label('Keterangan dan aturan khusus')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('code')->columns([
            TextColumn::make('code')->label('Kode')->weight('bold'),
            TextColumn::make('name')->label('Nama area')->searchable()->wrap(),
            TextColumn::make('hazard_class')->label('Kelas')->badge(),
            TextColumn::make('description')->label('Keterangan')->wrap()->limit(90)->toggleable(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAreas::route('/')];
    }
}
