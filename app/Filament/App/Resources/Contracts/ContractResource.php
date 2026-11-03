<?php

namespace App\Filament\App\Resources\Contracts;

use App\Enums\ContractStatus;
use App\Filament\App\Resources\Contracts\Pages\ManageContracts;
use App\Filament\Concerns\OwnerAccess;
use App\Models\Contract;
use App\Models\Worker;
use App\Services\MobilizationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Kontrak dan mobilisasi (IOGP 423 tahap 4): kick-off, bridging document, kartu akses, izin mulai kerja. */
class ContractResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'kontrak';

    protected static ?string $model = Contract::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Mobilisasi & Izin Kerja';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Kontrak';
    protected static ?string $pluralModelLabel = 'Kontrak & mobilisasi';

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        $mob = fn () => app(MobilizationService::class);

        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('number')->label('Nomor')->searchable(),
            TextColumn::make('title')->label('Pekerjaan')->weight('bold')->wrap()->searchable()->description(fn (Contract $c) => $c->contractor->name),
            TextColumn::make('workPlan.risk_category')->label('Risiko')->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('start_date')->label('Periode')->formatStateUsing(fn (Contract $c) => $c->start_date->translatedFormat('d M Y').' – '.$c->end_date->translatedFormat('d M Y')),
            TextColumn::make('kickoff_at')->label('Kick-off')->date('d M Y')->placeholder('belum'),
            TextColumn::make('cards')->label('Kartu aktif')->state(fn (Contract $c) => $c->accessCards()->usable()->count()),
        ])->filters([
            SelectFilter::make('status')->label('Status')->options(ContractStatus::class),
        ])->recordActions([ActionGroup::make([
            Action::make('kickoff')->label('Catat kick-off')->icon(Heroicon::OutlinedUserGroup)
                ->visible(fn (Contract $c) => static::canWrite() && ! $c->kickoff_at)
                ->schema([
                    Textarea::make('notes')->label('Notulen kick-off')->required(),
                    Textarea::make('bridging')->label('Bridging document (pembagian tanggung jawab K3)')->rows(5)
                        ->helperText('Wajib untuk pekerjaan risiko Tinggi, atau Menengah dengan Mode 2.'),
                ])
                ->action(fn (Contract $c, array $data) => static::rule(fn () => $mob()->kickoff($c, $data['notes'], $data['bridging'] ?? null), 'Kick-off dicatat')),
            Action::make('issue_cards')->label('Terbitkan kartu akses')->icon(Heroicon::OutlinedIdentification)
                ->visible(fn (Contract $c) => static::canWrite() && in_array($c->status, [ContractStatus::Mobilisasi, ContractStatus::Berjalan], true))
                ->requiresConfirmation()->modalDescription('Kartu diterbitkan untuk setiap pekerja rekanan yang surat sehat dan induksinya masih berlaku. Pekerja yang belum memenuhi syarat dilewati beserta alasannya.')
                ->action(function (Contract $c) use ($mob) {
                    $issued = 0;
                    $skipped = [];
                    foreach (Worker::where('contractor_id', $c->contractor_id)->where('is_active', true)->get() as $w) {
                        if ($c->accessCards()->usable()->where('worker_id', $w->id)->exists()) {
                            continue;
                        }
                        $why = $mob()->cardBlockers($c, $w);
                        if ($why) {
                            $skipped[] = implode(' ', $why);

                            continue;
                        }
                        $mob()->issueCard($c, $w);
                        $issued++;
                    }
                    Notification::make()->title("$issued kartu diterbitkan")->body($skipped ? "Dilewati:\n".implode("\n", $skipped) : null)->success()->persistent()->send();
                }),
            Action::make('approve_start')->label('Izinkan mulai kerja')->icon(Heroicon::OutlinedPlayCircle)->color('success')
                ->visible(fn (Contract $c) => static::role()?->canApprove() && $c->status === ContractStatus::Mobilisasi)
                ->requiresConfirmation()
                ->action(fn (Contract $c) => static::rule(fn () => $mob()->approveStart($c, auth()->user()), 'Pekerjaan boleh dimulai')),
            Action::make('cards_print')->label('Cetak kartu akses')->icon(Heroicon::OutlinedPrinter)->url(fn (Contract $c) => route('cetak.kartu-kontrak', $c), true),
        ])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContracts::route('/')];
    }
}
