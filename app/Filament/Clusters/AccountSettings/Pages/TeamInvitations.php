<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Filament\Widgets\PendingTeamInvitationsTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TeamInvitations extends Page
{
    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'team-invitations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.clusters.account-settings.pages.team-invitations';

    public function getTitle(): string
    {
        return 'Team invitations';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Team invitations';
    }

    public function getWidgets(): array
    {
        return [
            PendingTeamInvitationsTable::class,
        ];
    }
}
