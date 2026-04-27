<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;

it('logs out the user', function () {
    $user = User::factory()->create();

    actingAs($user);

    visit('/app')
        ->click('[aria-label="User menu"]')
        ->click('Sign out')
        ->assertPathIs('/');

    assertGuest();
});
