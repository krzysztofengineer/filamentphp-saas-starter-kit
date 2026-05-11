<?php

use App\Enums\BillingInterval;
use App\Enums\BillingPlan;
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

it('starts a checkout session for the chosen plan and interval', function (string $plan, string $interval, string $expectedPriceId) {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $capture = fakeStripeForBilling(
        checkoutRedirectUrl: route('billing.success', ['team' => $team]).'?session_id=cs_test_xyz'
    );

    actingAs($user);

    $page = visit('/app/'.$team->uuid.'/settings/subscription');

    if ($interval === 'yearly') {
        $page->click('[data-testid="billing-toggle-yearly"]');
    }

    $page->click('[data-testid="checkout-'.$plan.'-'.$interval.'"]')
        ->assertPathBeginsWith('/app/'.$team->uuid);

    expect($capture->sessionData)
        ->not->toBeNull()
        ->and($capture->sessionData['mode'])->toBe('subscription')
        ->and($capture->sessionData['line_items'][0]['price'])->toBe($expectedPriceId)
        ->and($capture->sessionData['line_items'][0]['quantity'])->toBe(1)
        ->and($capture->sessionData['allow_promotion_codes'] ?? null)->toBeTrue()
        ->and($capture->sessionData['success_url'])->toContain('session_id={CHECKOUT_SESSION_ID}')
        ->and($capture->sessionData['cancel_url'])->toBe(route('billing.cancel', ['team' => $team]))
        ->and($capture->sessionData['customer'])->toBe($capture->customerId);

    expect($team->fresh()->stripe_id)->toBe($capture->customerId);
})->with([
    'pro monthly' => ['pro', 'monthly', 'price_test_pro_monthly'],
    'pro yearly' => ['pro', 'yearly', 'price_test_pro_yearly'],
    'studio monthly' => ['studio', 'monthly', 'price_test_studio_monthly'],
    'studio yearly' => ['studio', 'yearly', 'price_test_studio_yearly'],
]);

it('reuses the existing customer when starting another checkout', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $team->forceFill(['stripe_id' => 'cus_existing_team'])->save();

    $capture = fakeStripeForBilling(
        checkoutRedirectUrl: route('billing.success', ['team' => $team]).'?session_id=cs_test_xyz'
    );

    actingAs($user);

    visit('/app/'.$team->uuid.'/settings/subscription')
        ->click('[data-testid="checkout-pro-monthly"]')
        ->assertPathBeginsWith('/app/'.$team->uuid);

    expect($capture->customerData)->toBeNull();
    expect($capture->sessionData['customer'])->toBe('cus_existing_team');
});

it('shows a success notice after returning from a completed checkout', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    actingAs($user);

    visit(route('billing.success', ['team' => $team]).'?session_id=cs_test_xyz')
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertSee('Thanks for subscribing!');
});

it('shows a cancellation notice after a cancelled checkout', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    actingAs($user);

    visit(route('billing.cancel', ['team' => $team]))
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertSee('Payment was not completed');
});

it('redirects to the billing portal for an existing subscription', function (BillingInterval $interval) {
    $admin = User::factory()->create();
    $team = Team::factory()->onPlan(BillingPlan::Pro, $interval)->create(['user_id' => $admin->id]);
    $admin->update(['current_team_id' => $team->id]);

    $capture = fakeStripeForBilling(
        portalRedirectUrl: '/app/'.$team->uuid
    );

    actingAs($admin);

    visit('/app/'.$team->uuid.'/settings/subscription')
        ->click('[data-testid="manage-subscription-'.$interval->value.'"]')
        ->assertPathBeginsWith('/app/'.$team->uuid);

    expect($capture->portalData)
        ->not->toBeNull()
        ->and($capture->portalData['customer'])->toBe($team->stripe_id)
        ->and($capture->portalData['return_url'])->toContain('/app/'.$team->uuid);
})->with([
    'monthly' => [BillingInterval::Monthly],
    'yearly' => [BillingInterval::Yearly],
]);
