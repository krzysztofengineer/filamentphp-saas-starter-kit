<?php

use App\BillingInterval;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;

it('shows the subscription plans', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/settings/subscription')
        ->assertPresent('[data-testid="plan-free"]')
        ->assertPresent('[data-testid="plan-pro"]')
        ->assertPresent('[data-testid="plan-studio"]')
        ->assertPresent('[data-testid="checkout-pro-monthly"]')
        ->assertPresent('[data-testid="checkout-studio-monthly"]')
        ->assertSee('Your current plan')
        ->assertNoJavaScriptErrors();
});

it('toggles between monthly and yearly prices', function () {
    $user = User::factory()->withTeam()->create();
    $tenant = $user->teams()->first();
    actingAs($user);

    // todo: first configure those prices config in this test

    visit('/app/'.$tenant->uuid.'/settings/subscription')
        ->assertSee('$29')
        ->assertSee('$79')
        ->click('[data-testid="billing-toggle-yearly"]')
        ->assertSee('$278')
        ->assertSee('$758')
        ->assertSee('Save 20%')
        ->click('[data-testid="billing-toggle-monthly"]')
        ->assertSee('$29')
        ->assertSee('$79')
        ->assertNoJavaScriptErrors();
});

it('marks the active plan with a manage subscription button when subscribed', function () {
    config()->set('billing.plans.pro.prices.monthly.stripe_id', 'price_test_pro_monthly');

    $admin = User::factory()->create();
    $team = Team::factory()->proPlan(BillingInterval::Monthly)->create(['user_id' => $admin->id]);
    $team->users()->attach($admin, ['role' => TeamRole::Administrator->value]);
    $admin->update(['current_team_id' => $team->id]);

    actingAs($admin);

    // todo: zbyt ogólne, asercje niczego nie sprawdzają

    visit('/app/'.$team->uuid.'/settings/subscription')
        ->assertSee('Manage subscription');
});
