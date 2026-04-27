<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertGuest;

it('lets a new user register, create a team and reach the dashboard', function () {
    visit('/app/register')
        ->fill('[data-testid="register-name"]', 'Alex Owner')
        ->fill('[data-testid="register-email"]', 'alex@example.com')
        ->fill('[data-testid="register-password"]', 'secret-password')
        ->fill('[data-testid="register-password-confirm"]', 'secret-password')
        ->click('[data-testid="register-terms"]')
        ->click('[data-testid="register-privacy"]')
        ->click('button[wire\\:target="register"]')
        ->assertSee('Create Team')
        ->fill('[data-testid="create-team-name"]', "Alex's Team")
        ->click('button[wire\\:target="register"]')
        ->assertSee('Welcome to')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('users', ['email' => 'alex@example.com', 'name' => 'Alex Owner']);
    assertDatabaseHas('teams', ['name' => "Alex's Team"]);
});

it('lets an existing user log in via the form', function () {
    User::factory()->withTeam()->create([
        'email' => 'existing@example.com',
        'password' => bcrypt('secret-password'),
    ]);

    visit('/app/login')
        ->fill('[data-testid="login-email"]', 'existing@example.com')
        ->fill('[data-testid="login-password"]', 'secret-password')
        ->click('[data-testid="login-submit"]')
        ->assertSee('Welcome to')
        ->assertNoJavaScriptErrors();
});

it('logs the user out from the user menu', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid)
        ->click('button[aria-label="User menu"]')
        ->click('Sign out')
        ->assertSee('already looking at.');

    assertGuest();
});
