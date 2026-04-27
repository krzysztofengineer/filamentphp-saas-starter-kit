<?php

use function Pest\Laravel\assertDatabaseHas;

it('allows users to register', function () {
    visit('/')
        ->click('@topbar-register')
        ->fill('@register-name', 'Test')
        ->fill('@register-email', 'test@example.com')
        ->fill('@register-password', 'password')
        ->fill('@register-password-confirm', 'password')
        ->click('@register-terms')
        ->click('@register-privacy')
        ->click('@register-submit')
        ->assertPathIs('/app');

    assertDatabaseHas('users', ['email' => 'test@example.com', 'name' => 'Test']);
    assertDatabaseHas('teams', ['name' => "Test's Team"]);
});
