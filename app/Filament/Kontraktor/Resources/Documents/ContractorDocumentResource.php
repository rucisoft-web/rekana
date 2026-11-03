<?php

namespace App\Filament\Kontraktor\Resources\Documents;

use App\Enums\DocumentType;
use App\Filament\Concerns\ContractorAccess;
use App\Filament\Kontraktor\Resources\Documents\Pages\ManageContractorDocuments;
use App\Models\ContractorDocument;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Dokumen legal dan K3 perusahaan; dipakai sebagai bukti PQ di semua owner. */
class ContractorDocumentResource extends Resource
{
    use ContractorAccess;

    protected static ?string $slug = 'dokumen';
    protected static ?string $model = ContractorDocument::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;
    protected static string|UnitEnum|null $navigationGroup = 'Perusahaan';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Dokumen';
    protected static ?string $pluralModelLabel = 'Dokumen perusahaan';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('type')->label('Jenis dokumen')->options(DocumentType::class)->required(),
            TextInput::make('title')->label('Nama dokumen')->required(),
            TextInput::make('number')->label('Nomor'),
            DatePicker::make('issued_at')->label('Tanggal terbit'),
            DatePicker::make('expires_at')->label('Berlaku sampai')->helperText('Kosongkan bila tidak berbatas waktu. Rekana mengingatkan 30 hari sebelum habis.'),
            FileUpload::make('file_path')->label('Berkas (PDF/JPG)')->disk('local')->directory('dokumen-rekanan')->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize((int) config('rekana.upload_max_kb')),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('expires_at')->columns([
            TextColumn::make('type')->label('Jenis')->badge(),
            TextColumn::make('title')->label('Dokumen')->weight('bold')->wrap()->searchable(),
            TextColumn::make('number')->label('Nomor'),
            TextColumn::make('expires_at')->label('Berlaku s.d.')->date('d M Y')->placeholder('tidak berbatas')
                ->color(fn ($state) => $state && $state->lte(today()->addDays(30)) ? 'danger' : null),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContractorDocuments::route('/')];
    }
}
