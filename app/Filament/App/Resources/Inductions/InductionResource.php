<?php

namespace App\Filament\App\Resources\Inductions;

use App\Filament\App\Resources\Inductions\Pages\ManageInductions;
use App\Filament\Concerns\OwnerAccess;
use App\Models\Contract;
use App\Models\Induction;
use App\Models\Worker;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Induksi K3 lokasi dengan pre-test dan post-test; kelulusan menjadi syarat kartu akses. */
class InductionResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'induksi';

    protected static ?string $model = Induction::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static string|UnitEnum|null $navigationGroup = 'Mobilisasi & Izin Kerja';
    protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'Induksi';
    protected static ?string $pluralModelLabel = 'Induksi & orientasi';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('title')->label('Judul induksi')->required()->columnSpan(2),
            DatePicker::make('held_at')->label('Tanggal')->required()->default(today()),
            Select::make('contract_id')->label('Kontrak')->relationship('contract', 'title')->live()->required(),
            Select::make('area_id')->label('Area')->relationship('area', 'name'),
            TextInput::make('trainer')->label('Pemateri'),
            TextInput::make('passing_score')->label('Nilai lulus post-test')->numeric()->default(70)->required(),
            TextInput::make('valid_months')->label('Berlaku (bulan)')->numeric()->default(12)->required(),
            CheckboxList::make('topics')->label('Topik')->options(config('rekana.induction_topics'))->columns(3)->columnSpanFull(),
            Repeater::make('attendees')->label('Peserta dan nilai')->relationship()->columnSpanFull()->columns(3)
                ->schema([
                    Select::make('worker_id')->label('Pekerja')->required()
                        ->options(fn (Get $get) => ($c = Contract::find($get('../../contract_id'))) ? Worker::where('contractor_id', $c->contractor_id)->pluck('name', 'id') : []),
                    TextInput::make('pre_score')->label('Pre-test')->numeric(),
                    TextInput::make('post_score')->label('Post-test')->numeric(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('held_at', 'desc')->columns([
            TextColumn::make('held_at')->label('Tanggal')->date('d M Y'),
            TextColumn::make('title')->label('Induksi')->weight('bold')->wrap()->description(fn (Induction $i) => $i->contract?->contractor?->name),
            TextColumn::make('attendees_count')->label('Peserta')->counts('attendees'),
            TextColumn::make('passed')->label('Lulus')->state(fn (Induction $i) => $i->attendees()->where('passed', true)->count().' / '.$i->attendees()->count()),
            TextColumn::make('trainer')->label('Pemateri')->toggleable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageInductions::route('/')];
    }
}
