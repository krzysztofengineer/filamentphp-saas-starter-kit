<?php

namespace App\Providers\Filament;

use App\Filament\Auth\PendingDeletion;
use App\Filament\AvatarProviders\UserAvatarProvider;
use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Filament\Clusters\AccountSettings\Pages\AccountSettings;
use App\Filament\Clusters\AccountSettings\Pages\TeamInvitations;
use App\Filament\Clusters\TeamSettings\Pages\TeamDetails;
use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\CustomRegister;
use App\Filament\Pages\CreateTeam;
use App\Filament\Pages\CustomDashboard;
use App\Http\Middleware\RedirectPendingDeletion;
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
                Action::make('teamSettings')
                    ->label('Team settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->extraAttributes(['data-testid' => 'team-settings'])
                    ->url(fn (): string => TeamDetails::getUrl()),
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
                'primary' => config('app.brand_color'),
            ])
            ->defaultAvatarProvider(UserAvatarProvider::class)
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->pages([
                CustomDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
                ->groups([
                    NavigationGroup::make()->items([
                        ...CustomDashboard::getNavigationItems(),
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
                RedirectPendingDeletion::class,
            ])
            ->authenticatedRoutes(fn (Panel $panel) => PendingDeletion::registerRoutes($panel))
            ->renderHook(PanelsRenderHook::HEAD_END, $forApp('partials.pwa-head'))
            ->brandLogoHeight('2rem');
    }
}
