<?php

use App\Models\User;

it('allows users to log in', function () {
    $user = User::factory()->create();

    visit('/')
        ->click('@topbar-login')
        ->fill('@login-email', $user->email)
        ->fill('@login-password', 'password')
        ->click('@login-submit')
        ->assertPathIs('/app/'.$user->teams()->first()->uuid)
        ->assertNoSmoke();
});
