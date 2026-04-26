<?php

namespace App\Providers\Filament;

use App\Filament\Home\Pages\Home;
use Filament\Facades\Filament;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HomePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $forHome = fn (string $view): \Closure => function () use ($view): Htmlable {
            if (Filament::getCurrentPanel()?->getId() !== 'home') {
                return new HtmlString('');
            }

            return view($view);
        };

        return $panel
            ->id('home')
            ->path('')
            ->viteTheme('resources/css/filament/home/theme.css')
            ->darkMode(false)
            ->brandLogo(fn () => view('logo'))
            ->favicon(asset('favicon.ico'))
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->topNavigation()
            ->discoverPages(in: app_path('Filament/Home/Pages'), for: 'App\Filament\Home\Pages')
            ->pages([
                Home::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER, $forHome('filament.home.topbar-nav'))
            ->renderHook(PanelsRenderHook::TOPBAR_END, $forHome('filament.home.topbar-login'))
            ->renderHook(PanelsRenderHook::HEAD_END, $forHome('partials.pwa-head'))
            ->renderHook(PanelsRenderHook::HEAD_END, $forHome('partials.seo-meta'));
    }
}
