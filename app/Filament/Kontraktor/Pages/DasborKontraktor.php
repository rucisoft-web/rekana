<?php

namespace App\Filament\Kontraktor\Pages;

use App\Services\ContractorPortalDashboard;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Dasbor kontraktor: pekerja, kontrak, izin kerja, dan hal yang harus diurus di semua owner. */
class DasborKontraktor extends Page
{
    protected static string $routePath = '/';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected static ?int $navigationSort = -10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Ringkasan';

    protected static ?string $navigationLabel = 'Dasbor Saya';

    protected static ?string $title = 'Dasbor Kontraktor';

    protected string $view = 'filament.pages.dasbor-kontraktor';

    public function getHeading(): string
    {
        return '';
    }

    public function getViewData(): array
    {
        return ['d' => app(ContractorPortalDashboard::class)->data(filament()->getTenant())];
    }
}
