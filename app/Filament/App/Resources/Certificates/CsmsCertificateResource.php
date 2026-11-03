<?php

namespace App\Filament\App\Resources\Certificates;

use App\Filament\App\Resources\Certificates\Pages\ManageCsmsCertificates;
use App\Filament\Concerns\OwnerAccess;
use App\Models\CsmsCertificate;
use App\Services\PrequalificationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Sertifikat CSMS hasil PQ; menentukan kategori risiko tertinggi yang boleh dikerjakan rekanan. */
class CsmsCertificateResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'sertifikat';

    protected static ?string $model = CsmsCertificate::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;
    protected static string|UnitEnum|null $navigationGroup = 'Prakualifikasi';
    protected static ?int $navigationSort = 30;
    protected static ?string $modelLabel = 'Sertifikat CSMS';
    protected static ?string $pluralModelLabel = 'Sertifikat CSMS';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('valid_until')->columns([
            TextColumn::make('number')->label('Nomor')->searchable(),
            TextColumn::make('contractor.name')->label('Rekanan')->weight('bold')->wrap()->searchable(),
            TextColumn::make('max_risk')->label('Boleh sampai risiko')->badge(),
            TextColumn::make('score')->label('Skor')->suffix('%'),
            TextColumn::make('issued_at')->label('Terbit')->date('d M Y'),
            TextColumn::make('valid_until')->label('Berlaku s.d.')->date('d M Y')
                ->color(fn (CsmsCertificate $c) => $c->revoked_at ? 'gray' : ($c->valid_until->lte(today()->addDays(config('rekana.dashboard.expiry_days'))) ? 'danger' : null))
                ->description(fn (CsmsCertificate $c) => $c->revoked_at ? 'Dicabut: '.$c->revoke_reason : null),
        ])->recordActions([
            Action::make('print')->label('Cetak')->icon(Heroicon::OutlinedPrinter)->url(fn (CsmsCertificate $c) => route('cetak.sertifikat', $c), true),
            Action::make('revoke')->label('Cabut')->icon(Heroicon::OutlinedNoSymbol)->color('danger')
                ->visible(fn (CsmsCertificate $c) => ! $c->revoked_at && static::role()?->canApprove())
                ->schema([Textarea::make('reason')->label('Alasan pencabutan')->required()])
                ->action(fn (CsmsCertificate $c, array $data) => static::rule(fn () => app(PrequalificationService::class)->revoke($c, $data['reason']), 'Sertifikat dicabut')),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCsmsCertificates::route('/')];
    }
}
