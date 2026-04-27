<?php

use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;

it('does not allow regular members to access team details', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);

    $team->members()->attach($user, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->assertNotPresent('Team details');
});
