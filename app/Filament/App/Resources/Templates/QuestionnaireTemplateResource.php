<?php

namespace App\Filament\App\Resources\Templates;

use App\Filament\App\Resources\Templates\Pages\ManageQuestionnaireTemplates;
use App\Filament\Concerns\OwnerAccess;
use App\Models\QuestionnaireTemplate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Template kuesioner prakualifikasi: template baku Rekana dan salinan milik perusahaan. */
class QuestionnaireTemplateResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'template-pq';

    protected static ?string $model = QuestionnaireTemplate::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 30;
    protected static ?string $modelLabel = 'Template PQ';
    protected static ?string $pluralModelLabel = 'Template kuesioner PQ';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->availableTo(filament()->getTenant())->withCount('items');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('standard')->label('Acuan')->columnSpanFull(),
            TextEntry::make('description')->label('Keterangan')->columnSpanFull(),
            RepeatableEntry::make('items')->label('Butir pertanyaan')->columnSpanFull()->schema([
                TextEntry::make('code')->label('Kode'),
                TextEntry::make('question')->label('Pertanyaan')->columnSpan(3),
                TextEntry::make('weight')->label('Bobot'),
                TextEntry::make('is_critical')->label('Kritis')->formatStateUsing(fn ($state) => $state ? 'Ya, nol = gagal' : 'Tidak'),
                TextEntry::make('min_risk')->label('Berlaku mulai risiko')->badge(),
            ])->columns(7),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Template')->weight('bold')->wrap()->description(fn (QuestionnaireTemplate $t) => $t->code),
            TextColumn::make('standard')->label('Acuan')->wrap(),
            TextColumn::make('items_count')->label('Butir'),
            TextColumn::make('owner_id')->label('Jenis')->formatStateUsing(fn ($state) => $state ? 'Milik perusahaan' : 'Baku Rekana')->badge()->default('baku'),
        ])->recordActions([
            ViewAction::make()->modalWidth('7xl'),
            Action::make('copy')->label('Salin untuk disesuaikan')->icon(Heroicon::OutlinedDocumentDuplicate)
                ->visible(fn (QuestionnaireTemplate $t) => $t->owner_id === null && static::canManage())
                ->requiresConfirmation()->action(fn (QuestionnaireTemplate $t) => static::rule(fn () => $t->copyFor(filament()->getTenant()), 'Template disalin')),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageQuestionnaireTemplates::route('/')];
    }
}
