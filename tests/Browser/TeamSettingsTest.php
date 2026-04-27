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
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create(['name' => 'Removable Member']);
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('Removable Member')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="remove-member-button"]')
        ->click('[data-testid="remove-member-confirm"]')
        ->assertSee('Member removed')
        ->assertNoJavaScriptErrors();

    assertDatabaseMissing('team_user', ['team_id' => $tenant->id, 'user_id' => $member->id]);
});

it('shows inline role selects for editable rows on the members page', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create(['name' => 'Promotable Member']);
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('Promotable Member')
        ->assertPresent('[data-testid="member-role-select"]');
});

it('deletes the team when the administrator types the name correctly', function () {
    $admin = User::factory()->create();
    $tenant = Team::factory()->create(['name' => 'Doomed Team', 'user_id' => $admin->id]);
    $tenant->users()->attach($admin, ['role' => TeamRole::Administrator->value]);
    $admin->update(['current_team_id' => $tenant->id]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="delete-team-button"]')
        ->fill('[data-testid="delete-team-name-input"]', 'Doomed Team')
        ->click('[data-testid="delete-team-confirm"]')
        ->assertPathIs('/app/new');

    assertDatabaseMissing('teams', ['id' => $tenant->id]);
});

it('blocks team deletion when the name confirmation does not match', function () {
    $admin = User::factory()->create();
    $tenant = Team::factory()->create(['name' => 'Doomed Team', 'user_id' => $admin->id]);
    $tenant->users()->attach($admin, ['role' => TeamRole::Administrator->value]);
    $admin->update(['current_team_id' => $tenant->id]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="delete-team-button"]')
        ->fill('[data-testid="delete-team-name-input"]', 'Wrong Name')
        ->click('[data-testid="delete-team-confirm"]')
        ->assertSee('selected')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('teams', ['id' => $tenant->id, 'name' => 'Doomed Team']);
});

it('blocks regular members from the team settings pages', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create();
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);
    $member->update(['current_team_id' => $tenant->id]);

    actingAs($member);

    $response = $this->get('/app/'.$tenant->uuid.'/settings/profile');
    expect($response->status())->toBe(403);
});

it('lets managers manage the team profile but hides the advanced page', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $manager = User::factory()->create();
    $tenant->users()->attach($manager, ['role' => TeamRole::Manager->value]);
    $manager->update(['current_team_id' => $tenant->id]);

    actingAs($manager);

    $profile = $this->get('/app/'.$tenant->uuid.'/settings/profile');
    expect($profile->status())->toBe(200);

    $advanced = $this->get('/app/'.$tenant->uuid.'/settings/advanced');
    expect($advanced->status())->toBe(403);
});
