<?php

namespace App\Filament\Clusters\AccountSettings;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class AccountSettingsCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $slug = 'account';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static bool $shouldRegisterNavigation = false;

    public static function getClusterBreadcrumb(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Account settings';
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }
}
