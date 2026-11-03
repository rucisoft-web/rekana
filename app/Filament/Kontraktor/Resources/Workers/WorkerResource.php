<?php

namespace App\Filament\Kontraktor\Resources\Workers;

use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Workers\Pages\ManageWorkers;
use App\Models\Worker;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Data pekerja, surat sehat, dan kompetensi; syarat kartu akses di lokasi owner. */
class WorkerResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'pekerja';
    protected static ?string $model = Worker::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static string|UnitEnum|null $navigationGroup = 'Pekerja';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Pekerja';
    protected static ?string $pluralModelLabel = 'Pekerja';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('name')->label('Nama lengkap')->required(),
            TextInput::make('nik')->label('NIK')->maxLength(20),
            TextInput::make('position')->label('Jabatan'),
            TextInput::make('phone')->label('Telepon'),
            DatePicker::make('birth_date')->label('Tanggal lahir'),
            DatePicker::make('medical_until')->label('Surat sehat berlaku s.d.')->helperText('Kartu akses otomatis tidak berlaku setelah tanggal ini.'),
            Toggle::make('is_active')->label('Aktif')->default(true),
            Repeater::make('competencies')->label('Kompetensi, SIO, dan sertifikat')->relationship()->columnSpanFull()->columns(4)->defaultItems(0)
                ->schema([
                    TextInput::make('name')->label('Nama kompetensi')->required(),
                    TextInput::make('number')->label('Nomor'),
                    TextInput::make('issuer')->label('Penerbit'),
                    DatePicker::make('expires_at')->label('Berlaku s.d.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('name')->columns([
            TextColumn::make('name')->label('Nama')->weight('bold')->searchable()->description(fn (Worker $w) => $w->position),
            TextColumn::make('medical_until')->label('Surat sehat s.d.')->date('d M Y')->placeholder('belum ada')
                ->color(fn ($state) => ! $state || $state->lte(today()->addDays(30)) ? 'danger' : null),
            TextColumn::make('competencies.name')->label('Kompetensi')->listWithLineBreaks()->limitList(3),
            IconColumn::make('is_active')->label('Aktif')->boolean(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageWorkers::route('/')];
    }
}
