<?php

namespace App\Filament\App\Pages;

use App\Services\ContractorDashboard;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Dasbor monitoring rekanan: kondisi seluruh kontraktor dalam satu layar untuk keputusan cepat. */
class DasborRekanan extends Page
{
    protected static string $routePath = '/';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected static ?int $navigationSort = -10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Ringkasan';

    protected static ?string $navigationLabel = 'Dasbor Monitoring';

    protected static ?string $title = 'Dasbor Monitoring Rekanan';

    protected string $view = 'filament.pages.dasbor-rekanan';

    public function getHeading(): string
    {
        return '';
    }

    public function getViewData(): array
    {
        return ['d' => app(ContractorDashboard::class)->data(filament()->getTenant())];
    }
}
