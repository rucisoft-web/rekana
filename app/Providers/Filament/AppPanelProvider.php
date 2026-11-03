<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\DasborEksekutif;
use App\Filament\App\Tenancy\DaftarInstitusi;
use App\Http\Middleware\RestrictDemoGuest;
use App\Http\Middleware\SetCurrentInstitution;
use App\Models\Institution;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $asset = fn (string $path) => asset($path).'?v='.@filemtime(public_path($path));

        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->passwordReset()
            ->profile()
            ->brandName('Akredia')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.2rem')
            ->favicon(asset('favicon.svg'))
            ->colors([
                // Biru akademik Rucisoft; 600 dipakai tombol utama.
                'primary' => [
                    50 => '#eef4fb', 100 => '#d9e6f5', 200 => '#b3cdea', 300 => '#82acd9', 400 => '#4f86c2',
                    500 => '#2f68a8', 600 => '#1f528c', 700 => '#17406f', 800 => '#14365c', 900 => '#112d4c', 950 => '#0b1d33',
                ],
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Emerald,
                'info' => Color::Sky,
            ])
            ->font('Inter')
            ->tenant(Institution::class, slugAttribute: 'slug', ownershipRelationship: 'institution')
            ->tenantRegistration(DaftarInstitusi::class)
            ->tenantMiddleware([SetCurrentInstitution::class], isPersistent: true)
            ->multiFactorAuthentication(
                [AppAuthentication::make()->recoverable()],
                // Akun tamu demo dipakai bersama sehingga tidak bisa memasang MFA.
                isRequired: fn () => config('akredia.mfa_required') && ! auth()->user()?->isDemoGuest(),
            )
            ->navigationGroups([
                NavigationGroup::make('Ringkasan'),
                NavigationGroup::make('Persiapan Akreditasi'),
                NavigationGroup::make('Pustaka Bukti'),
                NavigationGroup::make('Data Kinerja & LKPT'),
                NavigationGroup::make('Survei & Tracer')->collapsed(),
                NavigationGroup::make('Pengendalian'),
                NavigationGroup::make('SPMI')->collapsed(),
                NavigationGroup::make('Asesmen Lapangan')->collapsed(),
                NavigationGroup::make('Panduan Pimpinan'),
                NavigationGroup::make('Institusi')->collapsed(),
                NavigationGroup::make('Administrasi')->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([DasborEksekutif::class])
            ->widgets([])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->globalSearch(false)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => new HtmlString(
                '<link rel="stylesheet" href="'.$asset('css/akredia.css').'">'
                .'<script src="'.$asset('js/chart.umd.min.js').'"></script>'
                .'<script src="'.$asset('js/akredia.js').'" defer></script>'
            ))
            ->renderHook(PanelsRenderHook::FOOTER, fn () => view('filament.footer'))
            ->renderHook(PanelsRenderHook::BODY_START, fn () => view('filament.guest-ribbon'))
            ->renderHook(PanelsRenderHook::CONTENT_START, fn () => view('filament.demo-banner'))
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => view('filament.help.button'))
            ->renderHook(PanelsRenderHook::BODY_END, fn () => auth()->check() ? view('filament.help.panel') : '')
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn () => view('filament.login-links'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class, RestrictDemoGuest::class]);
    }
}
