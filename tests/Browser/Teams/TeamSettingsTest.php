<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('updates the team name from the profile form', function () {
    $user = User::factory()->create();
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/settings/profile')
        ->fill('[data-testid="team-details-name"]', 'Renamed Team')
        ->click('[data-testid="team-details-save"]')
        ->assertSeeIn('.fi-topbar', 'Renamed Team');

    assertDatabaseHas('teams', ['id' => $tenant->id, 'name' => 'Renamed Team']);
});

it('removes a member from the team', function () {
    $admin = User::factory()->create();
    $tenant = $admin->currentTeam;
    $member = User::factory()->create(['name' => 'Removable Member']);
    $tenant->members()->attach($member, ['role' => TeamRole::Member]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('Removable Member')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="remove-member"]')
        ->click('[data-testid="remove-member-confirm"]')
        ->assertSee('Member removed');

    assertDatabaseMissing('team_user', ['team_id' => $tenant->id, 'user_id' => $member->id]);
});

it('shows inline role selects for editable rows on the members page', function () {
    $admin = User::factory()->create();
    $tenant = $admin->currentTeam;
    $member = User::factory()->create(['name' => 'Promotable Member']);
    $tenant->members()->attach($member, ['role' => TeamRole::Member]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('Promotable Member')
        ->assertPresent('[data-testid="member-role-select"]');
});

it('deletes the team when the administrator types the name correctly', function () {
    $admin = User::factory()->create();
    $tenant = Team::factory()->create(['name' => 'Doomed Team', 'user_id' => $admin->id]);
    $admin->update(['current_team_id' => $tenant->id]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/profile')
        ->click('[data-testid="delete-team"]')
        ->fill('[data-testid="delete-team-name"]', 'Doomed Team')
        ->click('[data-testid="delete-team-confirm"]')
        ->assertSee('Team deleted');

    assertDatabaseMissing('teams', ['id' => $tenant->id]);
});

it('blocks team deletion when the name confirmation does not match', function () {
    $admin = User::factory()->create();
    $tenant = Team::factory()->create(['name' => 'Doomed Team', 'user_id' => $admin->id]);
    $admin->update(['current_team_id' => $tenant->id]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/profile')
        ->click('[data-testid="delete-team"]')
        ->fill('[data-testid="delete-team-name"]', 'Wrong Name')
        ->click('[data-testid="delete-team-confirm"]')
        ->assertSee('selected');

    assertDatabaseHas('teams', ['id' => $tenant->id, 'name' => 'Doomed Team']);
});

it('hides admin-only sections from regular members on the team settings page', function () {
    $admin = User::factory()->create();
    $tenant = Team::factory()->create(['user_id' => $admin->id]);
    $member = User::factory()->create();
    $tenant->members()->attach($member, ['role' => TeamRole::Member]);
    $member->update(['current_team_id' => $tenant->id]);

    actingAs($member);

    visit('/app/'.$tenant->uuid.'/settings/profile')
        ->assertNotPresent('[data-testid="team-details-name"]')
        ->assertNotPresent('[data-testid="transfer-ownership"]')
        ->assertNotPresent('[data-testid="delete-team"]')
        ->assertSee('Leave team');
});
