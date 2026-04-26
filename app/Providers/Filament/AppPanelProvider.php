<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Filament\Clusters\AccountSettings\Pages\AccountAdvanced;
use App\Filament\Clusters\AccountSettings\Pages\AccountBilling;
use App\Filament\Clusters\AccountSettings\Pages\AccountSettings;
use App\Filament\Clusters\TeamSettings\Pages\TeamProfile;
use App\Filament\Clusters\TeamSettings\TeamSettingsCluster;
use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\CustomRegister;
use App\Filament\Pages\CreateTeam;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\SaveCurrentTeam;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $forApp = fn (string $view): \Closure => function () use ($view): Htmlable {
            if (Filament::getCurrentPanel()?->getId() !== 'app') {
                return new HtmlString('');
            }

            return view($view);
        };

        return $panel
            ->default()
            ->id('app')
            ->brandLogo(fn () => view('logo'))
            ->favicon(asset('favicon.ico'))
            ->path('app')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->login(CustomLogin::class)
            ->registration(CustomRegister::class)
            ->passwordReset()
            ->topNavigation()
            ->tenant(Team::class)
            ->tenantRegistration(CreateTeam::class)
            ->tenantMenuItems([
                Action::make('teamSettings')
                    ->label('Team settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => TeamProfile::getUrl())
                    ->visible(fn (): bool => TeamSettingsCluster::canAccess()),
            ])
            ->userMenuItems([
                Action::make('accountSettings')
                    ->label('Account settings')
                    ->icon('heroicon-o-user')
                    ->url(fn (): ?string => filament()->getTenant() ? AccountSettings::getUrl() : null)
                    ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
                Action::make('accountBilling')
                    ->label('Subscription')
                    ->icon('heroicon-o-credit-card')
                    ->url(fn (): ?string => filament()->getTenant() ? AccountBilling::getUrl() : null)
                    ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
                Action::make('accountAdvanced')
                    ->label('Advanced')
                    ->icon('heroicon-o-shield-exclamation')
                    ->url(fn (): ?string => filament()->getTenant() ? AccountAdvanced::getUrl() : null)
                    ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
            ])
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
                ->groups([
                    NavigationGroup::make()->items([
                        ...Dashboard::getNavigationItems(),
                    ]),
                    NavigationGroup::make('Account')
                        ->items([
                            NavigationItem::make('Account settings')
                                ->icon('heroicon-o-user')
                                ->url(fn (): ?string => filament()->getTenant() ? AccountSettings::getUrl() : null)
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.app.account.pages.settings'))
                                ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
                            NavigationItem::make('Subscription')
                                ->icon('heroicon-o-credit-card')
                                ->url(fn (): ?string => filament()->getTenant() ? AccountBilling::getUrl() : null)
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.app.account.pages.subscription'))
                                ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
                            NavigationItem::make('Advanced')
                                ->icon('heroicon-o-shield-exclamation')
                                ->url(fn (): ?string => filament()->getTenant() ? AccountAdvanced::getUrl() : null)
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.app.account.pages.advanced'))
                                ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
                        ]),
                ]))
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
            ->tenantMiddleware([
                SaveCurrentTeam::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(PanelsRenderHook::HEAD_END, $forApp('partials.pwa-head'))
            ->brandLogoHeight('2rem');
    }
}
