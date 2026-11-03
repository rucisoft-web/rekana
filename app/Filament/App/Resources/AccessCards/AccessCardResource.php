<?php

namespace App\Filament\App\Resources\AccessCards;

use App\Enums\CardStatus;
use App\Filament\App\Resources\AccessCards\Pages\ManageAccessCards;
use App\Filament\Concerns\OwnerAccess;
use App\Models\AccessCard;
use App\Services\MobilizationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Kartu akses pekerja: berlaku sampai tanggal terdekat antara akhir kontrak, surat sehat, dan masa induksi. */
class AccessCardResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'kartu-akses';

    protected static ?string $model = AccessCard::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;
    protected static string|UnitEnum|null $navigationGroup = 'Mobilisasi & Izin Kerja';
    protected static ?int $navigationSort = 30;
    protected static ?string $modelLabel = 'Kartu akses';
    protected static ?string $pluralModelLabel = 'Kartu akses pekerja';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('valid_until')->columns([
            TextColumn::make('worker.name')->label('Pekerja')->weight('bold')->searchable()->description(fn (AccessCard $c) => $c->worker->position),
            TextColumn::make('contract.contractor.name')->label('Rekanan')->wrap(),
            TextColumn::make('contract.title')->label('Kontrak')->wrap()->limit(60)->toggleable(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('valid_until')->label('Berlaku s.d.')->date('d M Y')->color(fn (AccessCard $c) => $c->valid_until->lte(today()->addDays(14)) ? 'danger' : null),
            TextColumn::make('blocked_reason')->label('Catatan')->wrap()->placeholder('-')->toggleable(),
        ])->filters([
            SelectFilter::make('status')->label('Status')->options(CardStatus::class)->default('aktif'),
        ])->recordActions([
            Action::make('print')->label('Cetak')->icon(Heroicon::OutlinedPrinter)->url(fn (AccessCard $c) => route('cetak.kartu', $c), true),
            Action::make('block')->label('Blokir')->icon(Heroicon::OutlinedNoSymbol)->color('danger')
                ->visible(fn (AccessCard $c) => $c->status === CardStatus::Aktif && static::canWrite())
                ->schema([TextInput::make('reason')->label('Alasan')->required()])
                ->action(fn (AccessCard $c, array $data) => static::rule(fn () => app(MobilizationService::class)->blockCard($c, $data['reason']), 'Kartu diblokir')),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAccessCards::route('/')];
    }
}
