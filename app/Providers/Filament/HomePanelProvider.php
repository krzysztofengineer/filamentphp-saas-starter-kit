<?php

namespace App\Providers\Filament;

use App\Filament\Home\Pages\Home;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
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
            ->defaultThemeMode(ThemeMode::Dark)
            ->brandLogo(fn () => view('logo'))
            ->favicon(asset('favicon.ico'))
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => config('app.brand_color'),
            ])
            ->topNavigation()
            ->discoverPages(in: app_path('Filament/Home/Pages'), for: 'App\Filament\Home\Pages')
            ->pages([
                Home::class,
            ])
            ->navigationItems([
                NavigationItem::make('Features')->url('/#features')->sort(1),
                NavigationItem::make('Pricing')->url('/#pricing')->sort(2),
                NavigationItem::make('How it works')->url('/#how-it-works')->sort(3),
                NavigationItem::make('FAQ')->url('/#faq')->sort(4),
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
            ->renderHook(PanelsRenderHook::TOPBAR_END, $forHome('filament.home.topbar-login'))
            ->renderHook(PanelsRenderHook::HEAD_END, $forHome('partials.pwa-head'))
            ->renderHook(PanelsRenderHook::HEAD_END, $forHome('partials.seo-meta'));
    }
}
