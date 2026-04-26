<?php

use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('updates the team name from the profile form', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/settings/profile')
        ->fill('[data-testid="team-profile-name"]', 'Renamed Team')
        ->click('[data-testid="team-profile-save"]')
        ->assertSee('Team saved')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('teams', ['id' => $tenant->id, 'name' => 'Renamed Team']);
});

it('removes a member from the team', function () {
    $owner = User::factory()->withTeam()->create();
    $tenant = $owner->teams()->first();
    $member = User::factory()->create(['name' => 'Removable Member']);
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('Removable Member')
        ->click('[data-testid="remove-member-button"]')
        ->click('[data-testid="remove-member-confirm"]')
        ->assertSee('Member removed')
        ->assertNoJavaScriptErrors();

    assertDatabaseMissing('team_user', ['team_id' => $tenant->id, 'user_id' => $member->id]);
});

it('transfers team ownership to another member', function () {
    $owner = User::factory()->withTeam()->create();
    $tenant = $owner->teams()->first();
    $newOwner = User::factory()->create(['name' => 'Future Owner']);
    $tenant->users()->attach($newOwner, ['role' => TeamRole::Member->value]);

    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="transfer-ownership-button"]')
        ->select('select[wire\\:model*="new_owner_id"]', (string) $newOwner->id)
        ->click('[data-testid="transfer-ownership-confirm"]')
        ->assertSee('Ownership transferred')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('team_user', [
        'team_id' => $tenant->id,
        'user_id' => $newOwner->id,
        'role' => TeamRole::Owner->value,
    ]);
    assertDatabaseHas('team_user', [
        'team_id' => $tenant->id,
        'user_id' => $owner->id,
        'role' => TeamRole::Member->value,
    ]);
});

it('deletes the team when the owner types the name correctly', function () {
    $owner = User::factory()->create();
    $tenant = Team::factory()->create(['name' => 'Doomed Team', 'user_id' => $owner->id]);
    $tenant->users()->attach($owner, ['role' => TeamRole::Owner->value]);
    $owner->update(['current_team_id' => $tenant->id]);

    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="delete-team-button"]')
        ->fill('[data-testid="delete-team-name-input"]', 'Doomed Team')
        ->click('[data-testid="delete-team-confirm"]')
        ->assertPathIs('/app/new');

    assertDatabaseMissing('teams', ['id' => $tenant->id]);
});

it('blocks team deletion when the name confirmation does not match', function () {
    $owner = User::factory()->create();
    $tenant = Team::factory()->create(['name' => 'Doomed Team', 'user_id' => $owner->id]);
    $tenant->users()->attach($owner, ['role' => TeamRole::Owner->value]);
    $owner->update(['current_team_id' => $tenant->id]);

    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="delete-team-button"]')
        ->fill('[data-testid="delete-team-name-input"]', 'Wrong Name')
        ->click('[data-testid="delete-team-confirm"]')
        ->assertSee('selected')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('teams', ['id' => $tenant->id, 'name' => 'Doomed Team']);
});
