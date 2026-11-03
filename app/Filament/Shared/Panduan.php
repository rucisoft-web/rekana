<?php

namespace App\Filament\Shared;

use App\Support\Help;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Panduan penggunaan: semua entri bantuan satu panel dalam satu halaman, lengkap dengan tangkapan layar. */
abstract class Panduan extends Page
{
    protected string $view = 'filament.help.panduan';

    protected static ?string $slug = 'panduan';

    protected static ?string $title = 'Panduan Penggunaan';

    protected static ?string $navigationLabel = 'Panduan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|UnitEnum|null $navigationGroup = 'Ringkasan';

    protected static ?int $navigationSort = 90;

    abstract protected static function panel(): string;

    abstract protected function intro(): string;

    protected function getViewData(): array
    {
        return ['entries' => Help::forPanel(static::panel()), 'intro' => $this->intro()];
    }

    public static function urlFor(string $panel): ?string
    {
        $class = match ($panel) {
            'app' => \App\Filament\App\Pages\Panduan::class,
            'kontraktor' => \App\Filament\Kontraktor\Pages\Panduan::class,
            default => null,
        };

        return $class ? $class::getUrl() : null;
    }
}
