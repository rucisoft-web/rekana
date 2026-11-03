<?php

namespace App\Support;

use App\Models\LedSection;
use App\Models\WorkItem;

/** Isi tombol Bantuan: bantuan per halaman (resources/help) dan rujukan resmi BAN-PT per indikator. */
class Help
{
    protected static ?array $pages = null;

    protected static ?array $official = null;

    public static function pages(): array
    {
        return static::$pages ??= require resource_path('help/halaman.php');
    }

    /** Rujukan resmi Lampiran 3c + Panduan LED untuk indikator 1–39. */
    public static function official(?int $number = null): ?array
    {
        static::$official ??= require database_path('data/iapt41-resmi-3c.php');

        return $number === null ? static::$official : (static::$official[$number] ?? null);
    }

    /** Kunci bantuan dari nama rute panel, mis. filament.app.resources.documents.view → documents. */
    public static function keyFor(?string $route): string
    {
        $route ??= '';
        if (str_starts_with($route, 'filament.app.resources.')) {
            return explode('.', substr($route, strlen('filament.app.resources.')))[0];
        }
        if (str_starts_with($route, 'filament.app.pages.')) {
            $key = substr($route, strlen('filament.app.pages.'));

            return isset(static::pages()[$key]) || ! str_starts_with($key, 'panduan.') ? $key : 'panduan';
        }

        return match ($route) {
            'filament.app.auth.profile' => 'profile',
            default => '_umum',
        };
    }

    /** @return array{key: string, page: array, indicator: ?int, context: ?string} */
    public static function forCurrentRequest(): array
    {
        $routeObj = request()->route();
        $key = static::keyFor($routeObj?->getName());
        $page = static::pages()[$key] ?? static::pages()['_umum'];

        [$indicator, $context] = [null, null];
        $record = $routeObj?->parameter('record');
        if (is_scalar($record) && in_array($key, ['work-items', 'led-sections'], true)) {
            $item = $key === 'work-items'
                ? WorkItem::query()->with('subIndicator.indicator')->find($record)
                : LedSection::query()->with('workItem.subIndicator.indicator')->find($record)?->workItem;
            $sub = $item?->subIndicator;
            if ($sub?->indicator) {
                $indicator = (int) $sub->indicator->number;
                $context = 'Sub-indikator '.$sub->code.' di Akredia: '.$sub->statement;
            }
        }

        return compact('key', 'page', 'indicator', 'context');
    }
}
