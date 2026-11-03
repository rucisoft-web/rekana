<?php

namespace App\Support;

/** Isi tombol Bantuan per halaman (resources/help/halaman.php) dan tangkapan layar di public/img/bantuan. */
class Help
{
    protected static ?array $pages = null;

    public static function pages(): array
    {
        return static::$pages ??= require resource_path('help/halaman.php');
    }

    /**
     * Kunci bantuan dari nama rute panel:
     * filament.app.resources.izin-kerja.index → app.izin-kerja; ...prakualifikasi.edit → app.prakualifikasi.edit;
     * filament.kontraktor.pages.dasbor-kontraktor → kontraktor.dasbor-kontraktor.
     */
    public static function keyFor(?string $route): string
    {
        if (! preg_match('/^filament\.([a-z]+)\.(resources|pages)\.(.+)$/', (string) $route, $m)) {
            return '_umum';
        }
        [, $panel, $kind, $rest] = $m;

        if ($kind === 'pages') {
            return "{$panel}.{$rest}";
        }

        $parts = explode('.', $rest);
        $slug = $parts[0];
        $page = $parts[1] ?? 'index';
        $specific = "{$panel}.{$slug}.{$page}";

        return $page !== 'index' && isset(static::pages()[$specific]) ? $specific : "{$panel}.{$slug}";
    }

    public static function image(string $key): ?string
    {
        $file = 'img/bantuan/'.str_replace('.', '-', $key).'.png';

        return is_file(public_path($file)) ? asset($file) : null;
    }

    /** @return array{key: string, page: array, image: ?string} */
    public static function forCurrentRequest(): array
    {
        $key = static::keyFor(request()->route()?->getName());
        $page = static::pages()[$key] ?? static::pages()['_umum'];

        return ['key' => $key, 'page' => $page, 'image' => static::image($key)];
    }

    /** @return array<string, array> semua entri untuk satu panel, untuk halaman Panduan. */
    public static function forPanel(string $panel): array
    {
        return collect(static::pages())
            ->filter(fn ($page, $key) => str_starts_with($key, $panel.'.'))
            ->map(fn ($page, $key) => $page + ['key' => $key, 'image' => static::image($key)])
            ->all();
    }
}
