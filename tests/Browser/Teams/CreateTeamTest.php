<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('creates a new team from the create team page', function () {
    $user = User::factory()->create();
    actingAs($user);

    visit('/app/new')
        ->fill('@create-team-name', 'Side Project')
        ->click('@create-team-submit')
        ->assertNotPresent('@create-team-submit')
        ->assertSeeIn('.fi-topbar', 'Side Project');

    assertDatabaseHas('teams', ['name' => 'Side Project', 'user_id' => $user->id]);

    $team = $user->ownedTeams()->where('name', 'Side Project')->firstOrFail();

    assertDatabaseHas('team_user', [
        'team_id' => $team->id,
        'user_id' => $user->id,
        'role' => 'administrator',
    ]);
});
