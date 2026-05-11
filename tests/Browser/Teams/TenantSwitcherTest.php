<?php

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('switches between teams from the tenant menu', function () {
    $user = User::factory()->create();
    $personal = $user->currentTeam;

    $other = Team::factory()->create(['name' => 'Other Team', 'user_id' => $user->id]);

    actingAs($user);

    visit('/app/'.$personal->uuid)
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('.fi-dropdown-panel:visible a[href$="/app/'.$other->uuid.'"]')
        ->assertPathIs('/app/'.$other->uuid)
        ->assertSeeIn('.fi-topbar', 'Other Team');

    expect($user->fresh()->current_team_id)->toBe($other->id);
});

it('does not list the current team as a switch target', function () {
    $user = User::factory()->create();
    $personal = $user->currentTeam;

    actingAs($user);

    visit('/app/'.$personal->uuid)
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->assertNotPresent('.fi-dropdown-panel:visible a[href$="/app/'.$personal->uuid.'"]');
});
