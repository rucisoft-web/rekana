<?php

namespace App\Filament\Kontraktor\Resources\Permits;

use App\Enums\PermitStatus;
use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Permits\Pages\ManageWorkPermits;
use App\Models\WorkPermit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Izin kerja yang mencakup pekerja rekanan ini (hanya lihat; izin diterbitkan owner). */
class WorkPermitResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'izin-kerja';
    protected static ?string $model = WorkPermit::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;
    protected static string|UnitEnum|null $navigationGroup = 'Pekerjaan';
    protected static ?int $navigationSort = 40;
    protected static ?string $modelLabel = 'Izin kerja';
    protected static ?string $pluralModelLabel = 'Izin kerja';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('contract', fn ($q) => $q->withoutGlobalScopes()->where('contractor_id', filament()->getTenant()->id));
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('starts_at', 'desc')->columns([
            TextColumn::make('number')->label('Nomor'),
            TextColumn::make('title')->label('Pekerjaan')->wrap()->weight('bold')->description(fn (WorkPermit $p) => $p->contract->owner->name),
            TextColumn::make('type')->label('Jenis')->badge(),
            TextColumn::make('starts_at')->label('Waktu')->formatStateUsing(fn (WorkPermit $p) => $p->starts_at->translatedFormat('d M H:i').' – '.$p->ends_at->format('H:i')),
            TextColumn::make('workers.name')->label('Pekerja')->listWithLineBreaks()->limitList(3),
            TextColumn::make('status')->label('Status')->badge(),
        ])->filters([SelectFilter::make('status')->label('Status')->options(PermitStatus::class)->multiple()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageWorkPermits::route('/')];
    }
}
