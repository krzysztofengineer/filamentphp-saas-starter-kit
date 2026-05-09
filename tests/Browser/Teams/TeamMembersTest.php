<?php

use App\Enums\TeamRole;
use App\Filament\Widgets\TeamMembersTable;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('does not allow regular members to access team members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app/'.$team->uuid.'/settings/members')
        ->assertSee('403');
});

it('lists all team members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $otherUser = User::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $team->members()->attach($otherUser, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->assertSee($user->name)
        ->assertSee($otherUser->name);
});

it('removes team members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $member = User::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $team->members()->attach($member, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="remove-member"]')
        ->click('[data-testid="remove-member-confirm"]')
        ->assertSee('Member removed');

    assertDatabaseMissing('team_user', ['team_id' => $team->id, 'user_id' => $member->id]);
});

it('changes a member role', function () {
    $admin = User::factory()->create();
    $tenant = $admin->currentTeam;
    $member = User::factory()->create(['name' => 'Promotable Member']);
    $tenant->members()->attach($member, ['role' => TeamRole::Member]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->click('.fi-ta-record:has-text("Promotable Member") [data-testid="member-role-select"]')
        ->click('.fi-dropdown-panel:visible [data-value="administrator"]')
        ->assertSee('Role updated');

    assertDatabaseHas('team_user', [
        'team_id' => $tenant->id,
        'user_id' => $member->id,
        'role' => TeamRole::Administrator->value,
    ]);
});

it('cannot remove team owner', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Test', 'user_id' => $owner->id]);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->assertSee('Owner')
        ->assertNotPresent('button[aria-label="Actions"]');

    Filament::setTenant($team);

    Livewire::test(TeamMembersTable::class)
        ->call('mountAction', 'removeMember', [], ['table' => true, 'recordKey' => $owner->id])
        ->call('callMountedAction');

    assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $owner->id]);
});
