<?php

use App\Enums\BillingInterval;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    config()->set('billing.plans.pro.prices.monthly.label', '$29');
    config()->set('billing.plans.pro.prices.monthly.stripe_id', 'price_test_pro_monthly');
    config()->set('billing.plans.pro.prices.yearly.label', '$278');
    config()->set('billing.plans.pro.prices.yearly.stripe_id', 'price_test_pro_yearly');

    config()->set('billing.plans.studio.prices.monthly.label', '$79');
    config()->set('billing.plans.studio.prices.monthly.stripe_id', 'price_test_studio_monthly');
    config()->set('billing.plans.studio.prices.yearly.label', '$758');
    config()->set('billing.plans.studio.prices.yearly.stripe_id', 'price_test_studio_yearly');
});

it('shows the subscription plans', function () {
    $user = User::factory()->create();
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/settings/subscription')
        ->assertPresent('[data-testid="plan-free"]')
        ->assertPresent('[data-testid="plan-pro"]')
        ->assertPresent('[data-testid="plan-studio"]')
        ->assertPresent('[data-testid="checkout-pro-monthly"]')
        ->assertPresent('[data-testid="checkout-studio-monthly"]')
        ->assertSee('Your current plan');
});

it('toggles between monthly and yearly prices', function () {
    $user = User::factory()->create();
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/settings/subscription')
        ->assertSee('$29')
        ->assertSee('$79')
        ->click('[data-testid="billing-toggle-yearly"]')
        ->assertSee('$278')
        ->assertSee('$758')
        ->assertSee('Save 20%')
        ->click('[data-testid="billing-toggle-monthly"]')
        ->assertSee('$29')
        ->assertSee('$79');
});

it('marks the active plan with a manage subscription button when subscribed', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->proPlan(BillingInterval::Monthly)->create(['user_id' => $admin->id]);
    $admin->update(['current_team_id' => $team->id]);

    actingAs($admin);

    visit('/app/'.$team->uuid.'/settings/subscription')
        ->assertPresent('[data-testid="manage-subscription-monthly"]')
        ->assertNotPresent('[data-testid="checkout-pro-monthly"]')
        ->assertPresent('[data-testid="checkout-studio-monthly"]');
});
