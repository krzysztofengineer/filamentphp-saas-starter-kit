<?php

namespace App\Filament\Widgets;

use App\Filament\Clusters\AccountSettings\Pages\AccountSettings;
use App\Filament\Clusters\TeamSettings\Pages\TeamBilling;
use App\Filament\Clusters\TeamSettings\Pages\TeamDetails;
use App\Filament\Clusters\TeamSettings\Pages\TeamMembers;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardNavigation extends StatsOverviewWidget
{
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        return [
            Stat::make('Members', 'Team people')
                ->icon(Heroicon::OutlinedUsers)
                ->description('Manage who can access this team.')
                ->url(TeamMembers::getUrl()),
            Stat::make('Team settings', 'Profile')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->description('Update the team name and details.')
                ->url(TeamDetails::getUrl()),
            Stat::make('Subscription', 'Plan & invoices')
                ->icon(Heroicon::OutlinedCreditCard)
                ->description('Manage the team subscription and invoices.')
                ->url(TeamBilling::getUrl()),
            Stat::make('Account', 'Profile & security')
                ->icon(Heroicon::OutlinedUserCircle)
                ->description('Profile, password and security.')
                ->url(AccountSettings::getUrl()),
        ];
    }
}
