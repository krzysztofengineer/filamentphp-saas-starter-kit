<?php

use App\Actions\Teams\TransferTeamOwnership;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('lets a member leave a team they joined', function () {
    $owner = User::factory()->create();
    $hostTeam = Team::factory()->create(['name' => 'Acme', 'user_id' => $owner->id]);

    $user = User::factory()->create(['email' => 'visitor@example.com']);
    $hostTeam->members()->attach($user, ['role' => TeamRole::Member]);
    $user->update(['current_team_id' => $hostTeam->id]);

    actingAs($user);

    visit('/app/'.$hostTeam->uuid.'/settings/profile')
        ->assertSee('Leave team')
        ->click('[data-testid="leave-team"]')
        ->assertSee('You will lose access immediately')
        ->click('[data-testid="leave-team-confirm"]')
        ->assertSee('You left Acme');

    assertDatabaseMissing('team_user', ['team_id' => $hostTeam->id, 'user_id' => $user->id]);
});

it('hides the Leave section from a team owner', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com']);
    $team = Team::factory()->create(['name' => 'Owned Co', 'user_id' => $owner->id]);
    $owner->update(['current_team_id' => $team->id]);

    actingAs($owner);

    visit('/app/'.$team->uuid.'/settings/profile')
        ->assertNotPresent('[data-testid="leave-team"]');

    assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $owner->id]);
});

it('lets a former owner leave once ownership has been transferred', function () {
    $owner = User::factory()->create(['email' => 'former@example.com']);
    $team = Team::factory()->create(['name' => 'Handed Over Inc', 'user_id' => $owner->id]);
    $owner->update(['current_team_id' => $team->id]);

    $heir = User::factory()->create();
    $team->members()->attach($heir, ['role' => TeamRole::Administrator]);

    (new TransferTeamOwnership)->handle($team->fresh(), $owner, $heir);

    actingAs($owner);

    visit('/app/'.$team->uuid.'/settings/profile')
        ->assertSee('Leave team')
        ->click('[data-testid="leave-team"]')
        ->click('[data-testid="leave-team-confirm"]')
        ->assertSee('You left '.$team->name);

    assertDatabaseMissing('team_user', ['team_id' => $team->id, 'user_id' => $owner->id]);
});

it('disables destructive actions on a personal team and hides Leave', function () {
    $owner = User::factory()->create(['email' => 'personal@example.com']);

    actingAs($owner);

    visit('/app/'.$owner->currentTeam->uuid.'/settings/profile')
        ->assertNotPresent('[data-testid="leave-team"]')
        ->assertPresent('[data-testid="transfer-ownership"][aria-disabled="true"]')
        ->assertPresent('[data-testid="delete-team"][aria-disabled="true"]');
});

it('blocks leaving when the user is the only administrator of a team with other members', function () {
    $owner = User::factory()->create();
    $sharedTeam = Team::factory()->create(['name' => 'Solo Admin Inc', 'user_id' => $owner->id]);
    $sharedTeam->members()->detach($owner->id);
    $sharedTeam->members()->attach($owner, ['role' => TeamRole::Member]);

    $admin = User::factory()->create(['email' => 'sole-admin@example.com']);
    $sharedTeam->members()->attach($admin, ['role' => TeamRole::Administrator]);
    $admin->update(['current_team_id' => $sharedTeam->id]);

    actingAs($admin);

    visit('/app/'.$sharedTeam->uuid.'/settings/profile')
        ->assertSee('Leave team')
        ->click('[data-testid="leave-team"]')
        ->click('[data-testid="leave-team-confirm"]')
        ->assertSee('Transfer ownership first');

    assertDatabaseHas('team_user', ['team_id' => $sharedTeam->id, 'user_id' => $admin->id]);
});

it('lets a regular member view team settings but not Members', function () {
    $owner = User::factory()->create();
    $hostTeam = Team::factory()->create(['name' => 'Hosted', 'user_id' => $owner->id]);

    $user = User::factory()->create();
    $hostTeam->members()->attach($user, ['role' => TeamRole::Member]);
    $user->update(['current_team_id' => $hostTeam->id]);

    actingAs($user);

    visit('/app/'.$hostTeam->uuid.'/settings/members')
        ->assertSee('403');

    visit('/app/'.$hostTeam->uuid.'/settings/profile')
        ->assertSee('Leave team')
        ->assertNotPresent('[data-testid="team-details-name"]')
        ->assertNotPresent('[data-testid="delete-team"]')
        ->assertNotPresent('[data-testid="transfer-ownership"]');
});
