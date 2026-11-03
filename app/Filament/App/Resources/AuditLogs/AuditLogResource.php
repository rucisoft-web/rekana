<?php

namespace App\Filament\App\Resources\AuditLogs;

use App\Filament\App\Resources\AuditLogs\Pages\ManageAuditLogs;
use App\Filament\Concerns\OwnerAccess;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Jejak audit: siapa mengubah apa dan kapan. */
class AuditLogResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'log-audit';

    protected static ?string $model = AuditLog::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 90;
    protected static ?string $modelLabel = 'Log audit';
    protected static ?string $pluralModelLabel = 'Log audit';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('created_at')->label('Waktu')->dateTime('d/m/Y H:i'),
            TextColumn::make('user.name')->label('Pengguna')->placeholder('Sistem'),
            TextColumn::make('event')->label('Aksi')->badge(),
            TextColumn::make('auditable_type')->label('Objek')->formatStateUsing(fn ($state) => class_basename((string) $state)),
            TextColumn::make('label')->label('Keterangan')->wrap()->searchable(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAuditLogs::route('/')];
    }
}
