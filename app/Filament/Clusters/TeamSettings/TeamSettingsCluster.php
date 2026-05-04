<?php

namespace App\Filament\Clusters\TeamSettings;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class TeamSettingsCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $slug = 'settings';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getClusterBreadcrumb(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Settings';
    }

    public static function canAccess(): bool
    {
        return Filament::getTenant()?->users()->whereKey(auth()->id())->exists();
    }

    public static function canManage(): bool
    {
        return Auth::user()?->can('update', Filament::getTenant()) ?? false;
    }
}
