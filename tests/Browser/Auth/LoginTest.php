<?php

use App\Models\User;

use function Pest\Laravel\assertAuthenticated;

it('allows users to log in', function () {
    $user = User::factory()->create();

    visit('/')
        ->click('@topbar-login')
        ->assertPathIs('/app/login')
        ->fill('@login-email', $user->email)
        ->fill('@login-password', 'password')
        ->click('@login-submit')
        ->assertNotPresent('@login-submit')
        ->assertPathIs('/app/'.$user->teams()->first()->uuid)
        ->assertNoSmoke();

    assertAuthenticated();
});
