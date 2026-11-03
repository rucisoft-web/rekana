<?php

namespace App\Filament\App\Resources\Registrations;

use App\Enums\RegistrationStatus;
use App\Filament\App\Resources\Registrations\Pages\ManageContractorRegistrations;
use App\Filament\Concerns\OwnerAccess;
use App\Models\ContractorRegistration;
use App\Models\QuestionnaireTemplate;
use App\Enums\RiskLevel;
use App\Services\PrequalificationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Daftar rekanan yang terdaftar di perusahaan ini. Profil, dokumen, pekerja, dan statistik K3 diisi rekanan sendiri
 * lewat portal kontraktor; owner memverifikasi, memulai PQ, dan dapat memasukkan rekanan ke daftar hitam.
 */
class ContractorRegistrationResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'rekanan';

    protected static ?string $model = ContractorRegistration::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static string|UnitEnum|null $navigationGroup = 'Prakualifikasi';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Rekanan';
    protected static ?string $pluralModelLabel = 'Daftar rekanan';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profil perusahaan')->columns(3)->schema([
                TextEntry::make('contractor.name')->label('Nama'),
                TextEntry::make('contractor.business_field')->label('Bidang'),
                TextEntry::make('contractor.city')->label('Kota'),
                TextEntry::make('contractor.director_name')->label('Direktur'),
                TextEntry::make('contractor.nib')->label('NIB'),
                TextEntry::make('contractor.email')->label('Surel'),
                TextEntry::make('vendor_code')->label('Kode vendor'),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('blacklist_reason')->label('Alasan daftar hitam')->visible(fn ($record) => $record->blacklist_reason),
            ]),
            Section::make('Statistik K3 (diisi rekanan)')->schema([
                RepeatableEntry::make('contractor.safetyStatistics')->label('')->schema([
                    TextEntry::make('year')->label('Tahun'),
                    TextEntry::make('manhours')->label('Jam kerja')->numeric(thousandsSeparator: '.'),
                    TextEntry::make('fatalities')->label('Fatal'),
                    TextEntry::make('lti_cases')->label('LTI'),
                    TextEntry::make('recordable_cases')->label('Tercatat'),
                    TextEntry::make('ltifr')->label('LTIFR')->state(fn ($record) => $record->ltifr()),
                    TextEntry::make('trir')->label('TRIR')->state(fn ($record) => $record->trir()),
                ])->columns(7),
            ]),
            Section::make('Dokumen')->schema([
                RepeatableEntry::make('contractor.contractorDocuments')->label('')->schema([
                    TextEntry::make('type')->label('Jenis')->badge(),
                    TextEntry::make('title')->label('Dokumen')->columnSpan(2),
                    TextEntry::make('number')->label('Nomor'),
                    TextEntry::make('expires_at')->label('Berlaku s.d.')->date('d M Y')->placeholder('tidak berbatas')
                        ->color(fn ($state) => $state && $state->lte(today()->addDays(30)) ? 'danger' : null),
                ])->columns(5),
            ]),
            Section::make('Pekerja')->collapsed()->schema([
                RepeatableEntry::make('contractor.workers')->label('')->schema([
                    TextEntry::make('name')->label('Nama'),
                    TextEntry::make('position')->label('Jabatan'),
                    TextEntry::make('medical_until')->label('Surat sehat s.d.')->date('d M Y')->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),
                    TextEntry::make('competencies.name')->label('Kompetensi')->listWithLineBreaks()->columnSpan(2),
                ])->columns(5),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $owner = fn () => filament()->getTenant();

        return $table->columns([
            TextColumn::make('contractor.name')->label('Rekanan')->searchable()->weight('bold')->wrap()
                ->description(fn (ContractorRegistration $r) => $r->vendor_code.' · '.$r->contractor->business_field),
            TextColumn::make('contractor.city')->label('Kota')->toggleable(),
            TextColumn::make('status')->label('Registrasi')->badge(),
            TextColumn::make('pq')->label('PQ terakhir')->badge()
                ->state(fn (ContractorRegistration $r) => $r->contractor->prequalifications()->where('owner_id', $r->owner_id)->latest('id')->first()?->status?->getLabel() ?? 'Belum ada'),
            TextColumn::make('cert')->label('Sertifikat CSMS')
                ->state(fn (ContractorRegistration $r) => ($c = $r->contractor->validCertificate($r->owner_id)) ? $c->max_risk->getLabel().' s.d. '.$c->valid_until->translatedFormat('d M Y') : 'belum ada'),
            TextColumn::make('ltifr')->label('LTIFR 3 th')->state(fn (ContractorRegistration $r) => $r->contractor->safetySummary()['ltifr'] ?? '-')
                ->color(fn ($state) => is_numeric($state) && $state > config('rekana.dashboard.ltifr_alert') ? 'danger' : null),
        ])->filters([
            SelectFilter::make('status')->label('Status')->options(RegistrationStatus::class)->multiple(),
        ])->recordActions([
            ViewAction::make()->modalWidth('6xl'),
            ActionGroup::make([
                Action::make('activate')->label('Verifikasi / aktifkan')->icon(Heroicon::OutlinedCheckBadge)->color('success')
                    ->visible(fn (ContractorRegistration $r) => static::canManage() && $r->status !== RegistrationStatus::Aktif)
                    ->requiresConfirmation()
                    ->action(fn (ContractorRegistration $r) => $r->update(['status' => RegistrationStatus::Aktif, 'registered_at' => $r->registered_at ?? today(), 'blacklisted_at' => null, 'blacklist_reason' => null])),
                Action::make('start_pq')->label('Mulai prakualifikasi')->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->visible(fn (ContractorRegistration $r) => static::role()?->canEvaluate() && $r->status === RegistrationStatus::Aktif)
                    ->schema([
                        Select::make('template')->label('Template kuesioner')->required()
                            ->options(fn () => QuestionnaireTemplate::availableTo(filament()->getTenant())->pluck('name', 'id')),
                        Select::make('target')->label('Kategori risiko yang dituju')->options(RiskLevel::class)->required()->default('menengah')
                            ->helperText('Butir kuesioner disaring sesuai kategori; rekanan hanya bisa lulus sampai kategori ini.'),
                    ])
                    ->action(fn (ContractorRegistration $r, array $data) => static::rule(fn () => app(PrequalificationService::class)->start(
                        filament()->getTenant(), $r->contractor, QuestionnaireTemplate::findOrFail($data['template']), RiskLevel::from($data['target'] instanceof RiskLevel ? $data['target']->value : $data['target'])
                    ), 'Kuesioner PQ dikirim ke portal rekanan')),
                Action::make('blacklist')->label('Masukkan daftar hitam')->icon(Heroicon::OutlinedNoSymbol)->color('danger')
                    ->visible(fn (ContractorRegistration $r) => static::role()?->canApprove() && $r->status !== RegistrationStatus::DaftarHitam)
                    ->schema([Textarea::make('reason')->label('Alasan')->required()])
                    ->action(fn (ContractorRegistration $r, array $data) => $r->update(['status' => RegistrationStatus::DaftarHitam, 'blacklisted_by' => auth()->id(), 'blacklisted_at' => now(), 'blacklist_reason' => $data['reason']])),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContractorRegistrations::route('/')];
    }
}
