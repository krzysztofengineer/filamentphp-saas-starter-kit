<?php

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;

it('allows users to register', function () {
    visit('/')
        ->click('@topbar-register')
        ->assertPathIs('/app/register')
        ->fill('@register-name', 'Test')
        ->fill('@register-email', 'test@example.com')
        ->fill('@register-password', 'password')
        ->fill('@register-password-confirm', 'password')
        ->click('@register-terms')
        ->click('@register-privacy')
        ->click('@register-submit')
        ->assertNotPresent('@register-submit')
        ->assertPathIs('/app/email-verification/prompt')
        ->assertNoSmoke();

    assertAuthenticated();

    assertDatabaseHas('users', ['email' => 'test@example.com', 'name' => 'Test']);
    assertDatabaseHas('teams', ['name' => "Test's Team", 'user_id' => 1]);
    assertDatabaseHas('team_user', ['user_id' => 1, 'team_id' => 1, 'role' => 'administrator']);
});
