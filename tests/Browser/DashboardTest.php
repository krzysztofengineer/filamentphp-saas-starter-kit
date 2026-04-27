<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('shows the welcome card with the team name', function () {
    $user = User::factory()->withTeam()->create(['name' => 'Dana']);
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid)
        ->assertSee('Welcome to '.$tenant->name)
        ->assertSee('Hi Dana')
        ->assertNoJavaScriptErrors();
});

it('shows the quick action cards on the dashboard', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid)
        ->assertPresent('[data-testid="dashboard"]')
        ->assertSee('Members')
        ->assertSee('Team settings')
        ->assertSee('Subscription')
        ->assertSee('Account')
        ->assertNoJavaScriptErrors();
});

it('navigates from the dashboard to team members via the invite button', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid)
        ->click('Invite a member')
        ->assertPathIs('/app/'.$tenant->uuid.'/settings/members');
});

it('exposes a subscription link from the dashboard quick actions', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    $response = $this->get('/app/'.$tenant->uuid);
    expect($response->getContent())->toContain('/app/'.$tenant->uuid.'/settings/subscription');
});
