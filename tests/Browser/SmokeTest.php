<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('renders the home page without javascript errors', function () {
    visit('/')->assertNoJavaScriptErrors();
});

it('renders the privacy page without javascript errors', function () {
    visit('/privacy')->assertNoJavaScriptErrors();
});

it('renders the terms page without javascript errors', function () {
    visit('/terms')->assertNoJavaScriptErrors();
});

it('renders the login page without javascript errors', function () {
    visit('/app/login')->assertNoJavaScriptErrors();
});

it('renders the register page without javascript errors', function () {
    visit('/app/register')->assertNoJavaScriptErrors();
});

it('renders the dashboard without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid)->assertNoJavaScriptErrors();
});

it('renders the account settings page without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid.'/account/settings')->assertNoJavaScriptErrors();
});

it('renders the account billing page without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid.'/account/subscription')->assertNoJavaScriptErrors();
});

it('renders the account advanced page without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid.'/account/advanced')->assertNoJavaScriptErrors();
});

it('renders the team profile page without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid.'/settings/profile')->assertNoJavaScriptErrors();
});

it('renders the team members page without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid.'/settings/members')->assertNoJavaScriptErrors();
});

it('renders the team advanced page without javascript errors', function () {
    $user = User::factory()->withTeam()->create();
    actingAs($user);
    visit('/app/'.$user->teams()->first()->uuid.'/settings/advanced')->assertNoJavaScriptErrors();
});
