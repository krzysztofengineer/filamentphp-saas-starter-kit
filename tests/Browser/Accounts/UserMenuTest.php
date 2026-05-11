<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('navigates to account settings from the user menu', function () {
    $user = User::factory()->create();
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid)
        ->click('button.fi-user-menu-trigger')
        ->click('@user-menu-account-settings')
        ->assertPathIs('/app/'.$tenant->uuid.'/account/settings');
});

it('navigates to team invitations from the user menu', function () {
    $user = User::factory()->create();
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid)
        ->click('button.fi-user-menu-trigger')
        ->click('@user-menu-team-invitations')
        ->assertPathIs('/app/'.$tenant->uuid.'/account/team-invitations');
});
