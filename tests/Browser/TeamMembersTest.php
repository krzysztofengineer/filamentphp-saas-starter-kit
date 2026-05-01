<?php

use App\Filament\Widgets\TeamMembersTable;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertEquals;

it('does not allow regular members to access team members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->assertNotPresent('@team-members');
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
        ->click('@team-members')
        ->assertSee($user->name)
        ->assertSee($otherUser->name);
});

it('invites new members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit-button')
        ->assertNotPresent('@invite-submit-button')
        ->assertSee('test@example.com');

    assertDatabaseCount('invitations', 1);
    assertDatabaseHas('invitations', [
        'team_id' => $team->id,
        'email' => 'test@example.com',
    ]);
});

it('cannot invite the same email twice', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    Invitation::factory()->for($team)->create(['email' => 'test@example.com']);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit-button')
        ->assertSee('An invitation for that email already exists');

    assertDatabaseCount('invitations', 1);
});

it('cannot invite existing members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', $user->email)
        ->click('@invite-submit-button')
        ->assertSee('That user is already a member');
});

it('updates invitation role', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $invitation = Invitation::factory()->for($team)->member()->create(['email' => 'test@example.com']);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->click('[data-testid="invitation-role-select"]')
        ->click('.fi-dropdown-panel:visible [data-value="administrator"]')
        ->assertSee('Role updated');

    assertEquals(TeamRole::Administrator, $invitation->fresh()->role);
});

it('removes the invitation', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $invitation = Invitation::factory()->for($team)->create(['email' => 'test@example.com']);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="revoke-invitation-button"]')
        ->click('[data-testid="revoke-invitation-confirm"]')
        ->assertSee('Invitation revoked');

    assertDatabaseMissing('invitations', ['id' => $invitation->id]);
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
        ->click('@team-members')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="remove-member-button"]')
        ->click('[data-testid="remove-member-confirm"]')
        ->assertSee('Member removed');

    assertDatabaseMissing('team_user', ['team_id' => $team->id, 'user_id' => $member->id]);
});

it('cannot remove team owner', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Test', 'user_id' => $owner->id]);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($owner, ['role' => TeamRole::Administrator]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->assertSee('Owner')
        ->assertNotPresent('button[aria-label="Actions"]');

    Filament::setTenant($team);

    Livewire::test(TeamMembersTable::class)
        ->call('mountAction', 'removeMember', [], ['table' => true, 'recordKey' => $owner->id])
        ->call('callMountedAction');

    assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $owner->id]);
});
