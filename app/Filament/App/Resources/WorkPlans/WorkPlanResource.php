<?php

namespace App\Filament\App\Resources\WorkPlans;

use App\Enums\ContractMode;
use App\Enums\Hazard;
use App\Enums\WorkPlanStatus;
use App\Filament\App\Resources\WorkPlans\Pages\ManageWorkPlans;
use App\Filament\Concerns\OwnerAccess;
use App\Models\WorkPlan;
use App\Services\RiskAssessor;
use App\Services\TenderService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Rencana pekerjaan dan penilaian risikonya (IOGP 423 tahap 1). Kategori risiko menentukan seluruh syarat berikutnya. */
class WorkPlanResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'rencana-pekerjaan';

    protected static ?string $model = WorkPlan::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static string|UnitEnum|null $navigationGroup = 'Penilaian Risiko';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Rencana pekerjaan';
    protected static ?string $pluralModelLabel = 'Rencana pekerjaan & risiko';

    public static function form(Schema $schema): Schema
    {
        $scale = [1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'];

        return $schema->columns(2)->components([
            Section::make('Pekerjaan')->columns(2)->columnSpanFull()->schema([
                TextInput::make('title')->label('Judul pekerjaan')->required()->columnSpanFull(),
                Select::make('area_id')->label('Area')->relationship('area', 'name')->required(),
                Select::make('contract_mode')->label('Mode kontrak IOGP')->options(ContractMode::class)->required()->default('mode_2')
                    ->helperText('Mode 1: owner mengelola K3 dan kontraktor mengikuti. Mode 2: kontraktor memakai sistem K3-nya dengan aturan owner. Mode 3: kontraktor mengelola penuh dalam batas kontrak.'),
                DatePicker::make('planned_start')->label('Rencana mulai'),
                DatePicker::make('planned_end')->label('Rencana selesai'),
                TextInput::make('worker_estimate')->label('Perkiraan jumlah pekerja')->numeric(),
                TextInput::make('contract_value')->label('Perkiraan nilai (Rp)')->numeric(),
                Toggle::make('subcontracting')->label('Ada subkontraktor'),
                Textarea::make('scope')->label('Lingkup pekerjaan')->columnSpanFull(),
            ]),
            Repeater::make('riskLines')->label('Aktivitas dan bahaya (matriks 5×5)')->relationship()->columnSpanFull()->defaultItems(1)
                ->helperText('Skor = kemungkinan × keparahan. Skor ≥ 15 Tinggi, ≥ 6 Menengah. Bahaya seperti ruang terbatas, kerja panas, ketinggian, amonia, dan H2S langsung menjadikan pekerjaan Tinggi.')
                ->schema([
                    TextInput::make('activity')->label('Aktivitas')->required()->columnSpan(3),
                    Select::make('hazard')->label('Bahaya')->options(Hazard::class)->required()->columnSpan(2),
                    Select::make('likelihood')->label('Kemungkinan')->options($scale)->required(),
                    Select::make('severity')->label('Keparahan')->options($scale)->required(),
                    TextInput::make('controls')->label('Pengendalian')->columnSpan(5),
                ])->columns(7),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('number')->label('Nomor')->searchable()->placeholder('-'),
            TextColumn::make('title')->label('Pekerjaan')->searchable()->weight('bold')->wrap()->description(fn (WorkPlan $p) => $p->area?->name),
            TextColumn::make('contract_mode')->label('Mode')->badge(),
            TextColumn::make('risk_score')->label('Skor')->placeholder('-'),
            TextColumn::make('risk_category')->label('Risiko')->badge()->placeholder('Belum dinilai')
                ->description(fn (WorkPlan $p) => $p->forced_by ? 'dipaksa: '.collect($p->forced_by)->map(fn ($h) => Hazard::labelOf($h))->implode(', ') : null),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('planned_start')->label('Mulai')->date('d M Y')->toggleable(),
        ])->filters([
            SelectFilter::make('risk_category')->label('Risiko')->options(\App\Enums\RiskLevel::class),
            SelectFilter::make('status')->label('Status')->options(WorkPlanStatus::class),
        ])->recordActions([
            Action::make('assess')->label('Nilai risiko')->icon(Heroicon::OutlinedCalculator)->color('warning')
                ->visible(fn (WorkPlan $p) => static::canWrite() && in_array($p->status, [WorkPlanStatus::Draf, WorkPlanStatus::Dinilai], true))
                ->action(function (WorkPlan $p) {
                    $plan = static::rule(fn () => app(RiskAssessor::class)->assess($p, auth()->user()), 'Risiko dinilai');
                    if ($plan) {
                        \Filament\Notifications\Notification::make()->info()->title('Kewajiban untuk kategori '.$plan->risk_category->getLabel())
                            ->body(implode("\n", RiskAssessor::obligations($plan->risk_category)))->persistent()->send();
                    }
                }),
            Action::make('open_tender')->label('Buka seleksi')->icon(Heroicon::OutlinedMegaphone)
                ->visible(fn (WorkPlan $p) => static::role()?->canProcure() && $p->status === WorkPlanStatus::Dinilai)
                ->schema([
                    TextInput::make('title')->label('Judul seleksi')->default(fn (WorkPlan $p) => $p->title)->required(),
                    DateTimePicker::make('closes_at')->label('Batas unggah HSE Plan')->default(now()->addDays(10))->required(),
                    TextInput::make('hse_pass_score')->label('Ambang lulus HSE Plan (%)')->numeric()->default(65)->required(),
                    TextInput::make('hse_weight')->label('Bobot HSE dalam nilai akhir (%)')->numeric()->default(30)->required(),
                ])
                ->action(fn (WorkPlan $p, array $data) => static::rule(fn () => app(TenderService::class)->open($p, $data), 'Seleksi dibuka; undang rekanan dari menu Seleksi')),
            EditAction::make()->visible(fn (WorkPlan $p) => static::canWrite() && in_array($p->status, [WorkPlanStatus::Draf, WorkPlanStatus::Dinilai], true)),
            DeleteAction::make()->visible(fn (WorkPlan $p) => $p->status === WorkPlanStatus::Draf),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageWorkPlans::route('/')];
    }
}
