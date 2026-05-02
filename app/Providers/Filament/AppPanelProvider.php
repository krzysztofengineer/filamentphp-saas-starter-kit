<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Filament\Clusters\AccountSettings\Pages\AccountAdvanced;
use App\Filament\Clusters\AccountSettings\Pages\AccountSettings;
use App\Filament\Clusters\AccountSettings\Pages\TeamInvitations;
use App\Filament\Clusters\TeamSettings\Pages\TeamAdvanced;
use App\Filament\Clusters\TeamSettings\Pages\TeamBilling;
use App\Filament\Clusters\TeamSettings\Pages\TeamDetails;
use App\Filament\Clusters\TeamSettings\Pages\TeamMembers;
use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\CustomRegister;
use App\Filament\Pages\CreateTeam;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\SaveCurrentTeam;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Enums\ThemeMode;
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
            ->defaultThemeMode(ThemeMode::Dark)
            ->login(CustomLogin::class)
            ->registration(CustomRegister::class)
            ->passwordReset()
            ->emailVerification()
            ->topNavigation()
            ->tenant(Team::class)
            ->tenantRegistration(CreateTeam::class)
            ->tenantMenuItems([
                Action::make('teamDetails')
                    ->label('Team details')
                    ->icon('heroicon-o-identification')
                    ->extraAttributes(['data-testid' => 'team-details'])
                    ->url(fn (): string => TeamDetails::getUrl())
                    ->visible(fn (): bool => TeamDetails::canAccess()),
                Action::make('teamMembers')
                    ->label('Members')
                    ->icon('heroicon-o-users')
                    ->extraAttributes(['data-testid' => 'team-members'])
                    ->url(fn (): string => TeamMembers::getUrl())
                    ->visible(fn (): bool => TeamMembers::canAccess()),
                Action::make('teamBilling')
                    ->label('Subscription')
                    ->icon('heroicon-o-credit-card')
                    ->url(fn (): string => TeamBilling::getUrl())
                    ->visible(fn (): bool => TeamBilling::canAccess()),
                Action::make('teamAdvanced')
                    ->label('Advanced')
                    ->icon('heroicon-o-shield-exclamation')
                    ->url(fn (): string => TeamAdvanced::getUrl())
                    ->visible(fn (): bool => TeamAdvanced::canAccess()),
            ])
            ->userMenuItems([
                Action::make('accountSettings')
                    ->label('Account settings')
                    ->icon('heroicon-o-user')
                    ->url(fn (): ?string => filament()->getTenant() ? AccountSettings::getUrl() : null)
                    ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
                Action::make('teamInvitations')
                    ->label('Team invitations')
                    ->icon('heroicon-o-envelope-open')
                    ->extraAttributes(['data-testid' => 'user-menu-team-invitations'])
                    ->url(fn (): ?string => filament()->getTenant() ? TeamInvitations::getUrl() : null)
                    ->visible(fn (): bool => filament()->getTenant() !== null && AccountSettingsCluster::canAccess()),
            ])
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Red,
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
