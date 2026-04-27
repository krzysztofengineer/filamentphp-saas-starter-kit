<?php

namespace App\Filament\Clusters\TeamSettings;

use App\Models\Team;
use App\Models\User;
use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

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
        $tenant = Filament::getTenant();
        $user = auth()->user();

        if (! $tenant instanceof Team || ! $user instanceof User) {
            return false;
        }

        return $tenant->canBeManagedBy($user);
    }
}
