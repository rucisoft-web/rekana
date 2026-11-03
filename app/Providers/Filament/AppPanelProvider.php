<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\DasborRekanan;
use App\Filament\App\Tenancy\BelumAdaAkses;
use App\Http\Middleware\RestrictDemoGuest;
use App\Http\Middleware\SetCurrentOwner;
use App\Models\Owner;
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

/** Panel owner (perusahaan pemberi kerja): tenant = Owner. */
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
            ->brandName('Rekana')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.1rem')
            ->favicon(asset('favicon.svg'))
            ->colors([
                // Kuning-jingga keselamatan industri (safety amber); 600 dipakai tombol utama.
                'primary' => [
                    50 => '#fff8eb', 100 => '#feeac7', 200 => '#fdd28a', 300 => '#fcb44d', 400 => '#fb9a24',
                    500 => '#f07a0b', 600 => '#d45a06', 700 => '#b03f09', 800 => '#8f310e', 900 => '#75290f', 950 => '#431303',
                ],
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'info' => Color::Sky,
                'gray' => Color::Stone,
            ])
            ->font('Inter')
            ->tenant(Owner::class, slugAttribute: 'slug', ownershipRelationship: 'owner')
            ->tenantRegistration(BelumAdaAkses::class)
            ->tenantMiddleware([SetCurrentOwner::class], isPersistent: true)
            ->multiFactorAuthentication(
                [AppAuthentication::make()->recoverable()],
                isRequired: fn () => config('rekana.mfa_required') && ! auth()->user()?->isDemoGuest(),
            )
            ->navigationGroups([
                NavigationGroup::make('Ringkasan'),
                NavigationGroup::make('Penilaian Risiko'),
                NavigationGroup::make('Prakualifikasi'),
                NavigationGroup::make('Seleksi'),
                NavigationGroup::make('Mobilisasi & Izin Kerja'),
                NavigationGroup::make('Pengaturan')->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([DasborRekanan::class])
            ->widgets([])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->globalSearch(false)
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => new HtmlString(
                '<link rel="stylesheet" href="'.$asset('css/rekana.css').'">'
                .'<script src="'.$asset('js/chart.umd.min.js').'"></script>'
                .'<script src="'.$asset('js/rekana.js').'" defer></script>'
            ))
            ->renderHook(PanelsRenderHook::FOOTER, fn () => view('filament.footer'))
            ->renderHook(PanelsRenderHook::BODY_START, fn () => view('filament.guest-ribbon'))
            ->renderHook(PanelsRenderHook::CONTENT_START, fn () => view('filament.demo-banner'))
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
