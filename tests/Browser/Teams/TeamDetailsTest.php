<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;

it('does not allow regular members to access team details', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);

    $team->members()->attach($user, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->assertNotPresent('Team details');
});

it('updates the team name', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);

    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app/'.$team->uuid.'/settings/profile')
        ->fill('@team-details-name', 'New name')
        ->click('@team-details-save')
        ->assertSeeIn('.fi-topbar', 'New name');

    assertEquals('New name', $team->fresh()->name);
});
