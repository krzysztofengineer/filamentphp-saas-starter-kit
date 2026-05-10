<?php

namespace App\Filament\Clusters\TeamSettings\Pages;

use App\Filament\Clusters\TeamSettings\TeamSettingsCluster;
use App\Filament\Widgets\TeamInvitationsTable;
use App\Filament\Widgets\TeamMembersTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TeamMembers extends Page
{
    protected static ?string $cluster = TeamSettingsCluster::class;

    protected static ?string $slug = 'members';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return TeamSettingsCluster::canManage();
    }

    public function getTitle(): string
    {
        return 'Members';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Members';
    }

    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0]->extraAttributes([
                'data-testid' => 'team-members',
            ]),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(TeamMembersTable::class)->key('team-members-table'),
            Livewire::make(TeamInvitationsTable::class)->key('team-invitations-table'),
        ]);
    }
}
