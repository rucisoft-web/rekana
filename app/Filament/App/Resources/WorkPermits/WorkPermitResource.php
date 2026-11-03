<?php

namespace App\Filament\App\Resources\WorkPermits;

use App\Enums\ContractStatus;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Filament\App\Resources\WorkPermits\Pages\ManageWorkPermits;
use App\Filament\Concerns\OwnerAccess;
use App\Models\AccessCard;
use App\Models\Contract;
use App\Models\WorkPermit;
use App\Services\PermitService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Izin kerja (Permit to Work) digital: daftar periksa, uji gas, isolasi energi, maksimal satu shift. */
class WorkPermitResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'izin-kerja';

    protected static ?string $model = WorkPermit::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;
    protected static string|UnitEnum|null $navigationGroup = 'Mobilisasi & Izin Kerja';
    protected static ?int $navigationSort = 40;
    protected static ?string $modelLabel = 'Izin kerja';
    protected static ?string $pluralModelLabel = 'Izin kerja';

    public static function canEdit(Model $record): bool { return static::canWrite() && $record->status === PermitStatus::Draf; }
    public static function canDelete(Model $record): bool { return static::canWrite() && $record->status === PermitStatus::Draf; }

    public static function getNavigationBadge(): ?string
    {
        $n = WorkPermit::where('status', PermitStatus::Diajukan)->count();

        return $n ? (string) $n : null;
    }

    public static function form(Schema $schema): Schema
    {
        $type = fn (Get $get) => PermitType::tryFrom($get('type') instanceof PermitType ? $get('type')->value : (string) $get('type'));

        return $schema->columns(3)->components([
            Section::make('Pekerjaan')->columns(3)->columnSpanFull()->schema([
                Select::make('contract_id')->label('Kontrak')->required()->live()
                    ->options(fn () => Contract::whereIn('status', [ContractStatus::Berjalan, ContractStatus::Mobilisasi])->with('contractor')->get()->mapWithKeys(fn ($c) => [$c->id => $c->contractor->name.' · '.$c->title])),
                Select::make('type')->label('Jenis izin')->options(PermitType::class)->required()->live(),
                Select::make('area_id')->label('Area')->relationship('area', 'name')->required(),
                TextInput::make('title')->label('Uraian pekerjaan')->required()->columnSpan(2),
                TextInput::make('location_detail')->label('Lokasi rinci'),
                DateTimePicker::make('starts_at')->label('Mulai')->required()->seconds(false),
                DateTimePicker::make('ends_at')->label('Selesai')->required()->seconds(false)->helperText('Paling lama '.config('rekana.permit_max_hours').' jam (satu shift).'),
                Select::make('workers')->label('Pekerja')->multiple()->relationship('workers', 'name')->preload()
                    ->options(fn (Get $get) => AccessCard::usable()->where('contract_id', $get('contract_id'))->with('worker')->get()->pluck('worker.name', 'worker_id'))
                    ->helperText('Hanya pekerja dengan kartu akses aktif untuk kontrak ini.'),
                Textarea::make('description')->label('Langkah kerja dan bahaya')->columnSpanFull(),
            ]),
            Section::make('Daftar periksa')->columnSpanFull()->schema([
                CheckboxList::make('checklist')->hiddenLabel()->columns(2)
                    ->options(fn (Get $get) => $type($get)?->checklist() ?? [])
                    ->formatStateUsing(fn ($state) => collect((array) $state)->filter()->keys()->all())
                    ->dehydrateStateUsing(fn ($state) => collect((array) $state)->mapWithKeys(fn ($k) => [$k => true])->all()),
            ]),
            Section::make('Uji gas')->columns(4)->columnSpanFull()
                ->description('O2 '.config('rekana.gas_limits.o2_min').'–'.config('rekana.gas_limits.o2_max').'% · LEL ≤ '.config('rekana.gas_limits.lel_max').'% · H2S ≤ '.config('rekana.gas_limits.h2s_max').' ppm · CO ≤ '.config('rekana.gas_limits.co_max').' ppm')
                ->visible(fn (Get $get) => in_array($type($get), [PermitType::RuangTerbatas, PermitType::KerjaPanas], true))
                ->schema([
                    TextInput::make('gas_test.o2')->label('O2 (%)')->numeric(),
                    TextInput::make('gas_test.lel')->label('LEL (%)')->numeric(),
                    TextInput::make('gas_test.h2s')->label('H2S (ppm)')->numeric(),
                    TextInput::make('gas_test.co')->label('CO (ppm)')->numeric(),
                ]),
            Textarea::make('isolation_points')->label('Titik isolasi energi')->columnSpanFull()
                ->visible(fn (Get $get) => $type($get)?->needsIsolationPoints()),
        ]);
    }

    public static function table(Table $table): Table
    {
        $svc = fn () => app(PermitService::class);

        return $table->defaultSort('starts_at', 'desc')->columns([
            TextColumn::make('number')->label('Nomor')->searchable(),
            TextColumn::make('title')->label('Pekerjaan')->wrap()->weight('bold')->searchable()->description(fn (WorkPermit $p) => $p->contract->contractor->name),
            TextColumn::make('type')->label('Jenis')->badge(),
            TextColumn::make('area.name')->label('Area')->wrap()->toggleable(),
            TextColumn::make('starts_at')->label('Waktu')->formatStateUsing(fn (WorkPermit $p) => $p->starts_at->translatedFormat('d M H:i').' – '.$p->ends_at->format('H:i')),
            TextColumn::make('workers.name')->label('Pekerja')->listWithLineBreaks()->limitList(3)->toggleable(),
            TextColumn::make('status')->label('Status')->badge(),
        ])->filters([
            SelectFilter::make('status')->label('Status')->options(PermitStatus::class)->multiple(),
            SelectFilter::make('type')->label('Jenis')->options(PermitType::class),
        ])->recordActions([
            ViewAction::make(),
            EditAction::make(),
            ActionGroup::make([
                Action::make('submit')->label('Ajukan')->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (WorkPermit $p) => $p->status === PermitStatus::Draf && static::canWrite())
                    ->action(fn (WorkPermit $p) => static::rule(fn () => $svc()->submit($p, auth()->user()), 'Izin diajukan')),
                Action::make('approve')->label('Setujui')->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (WorkPermit $p) => $p->status === PermitStatus::Diajukan && static::role()?->canAuthorizePermit())
                    ->requiresConfirmation()->action(fn (WorkPermit $p) => static::rule(fn () => $svc()->approve($p, auth()->user()), 'Izin disetujui')),
                Action::make('activate')->label('Aktifkan di lokasi')->icon(Heroicon::OutlinedPlay)->color('warning')
                    ->visible(fn (WorkPermit $p) => $p->status === PermitStatus::Disetujui && static::canWrite())
                    ->requiresConfirmation()->action(fn (WorkPermit $p) => static::rule(fn () => $svc()->activate($p), 'Izin aktif')),
                Action::make('close')->label('Tutup izin')->icon(Heroicon::OutlinedLockClosed)
                    ->visible(fn (WorkPermit $p) => $p->status === PermitStatus::Aktif && static::canWrite())
                    ->schema([Textarea::make('notes')->label('Catatan penutupan')->default('Pekerjaan selesai, area dirapikan, isolasi dilepas.')->required()])
                    ->action(fn (WorkPermit $p, array $data) => static::rule(fn () => $svc()->close($p, auth()->user(), $data['notes']), 'Izin ditutup')),
                Action::make('cancel')->label('Batalkan')->icon(Heroicon::OutlinedXCircle)->color('danger')
                    ->visible(fn (WorkPermit $p) => in_array($p->status, [PermitStatus::Draf, PermitStatus::Diajukan, PermitStatus::Disetujui, PermitStatus::Aktif], true) && static::canWrite())
                    ->schema([Textarea::make('reason')->label('Alasan')->required()])
                    ->action(fn (WorkPermit $p, array $data) => static::rule(fn () => $svc()->cancel($p, $data['reason']), 'Izin dibatalkan')),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageWorkPermits::route('/')];
    }
}
