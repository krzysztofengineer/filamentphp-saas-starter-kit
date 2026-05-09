<?php

use App\BillingInterval;
use App\BillingPlan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Mockery as m;
use Stripe\BillingPortal\Session as StripePortalSession;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Customer as StripeCustomer;
use Stripe\StripeClient;

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

if (! function_exists('fakeStripeForBilling')) {
    function fakeStripeForBilling(?string $checkoutRedirectUrl = null, ?string $portalRedirectUrl = null): object
    {
        $capture = new class
        {
            public ?array $sessionData = null;

            public ?array $portalData = null;

            public ?array $customerData = null;

            public string $sessionId;

            public string $customerId;
        };

        $capture->sessionId = 'cs_test_'.Str::random(14);
        $capture->customerId = 'cus_test_'.Str::random(14);

        $sessionsService = m::mock();
        $sessionsService->shouldReceive('create')
            ->andReturnUsing(function (array $data) use ($capture, $checkoutRedirectUrl) {
                $capture->sessionData = $data;

                return StripeSession::constructFrom([
                    'id' => $capture->sessionId,
                    'url' => $checkoutRedirectUrl ?? 'https://checkout.stripe.test/'.$capture->sessionId,
                ]);
            });

        $customersService = m::mock();
        $customersService->shouldReceive('create')
            ->andReturnUsing(function (array $data, array $requestOptions = []) use ($capture) {
                $capture->customerData = $data;

                return StripeCustomer::constructFrom([
                    'id' => $capture->customerId,
                    'email' => $data['email'] ?? null,
                    'name' => $data['name'] ?? null,
                ]);
            });
        $customersService->shouldReceive('retrieve')
            ->andReturnUsing(fn (string $id, array $opts = []) => StripeCustomer::constructFrom(['id' => $id]));
        $customersService->shouldReceive('update')
            ->andReturnUsing(fn (string $id, array $data = []) => StripeCustomer::constructFrom(['id' => $id]));

        $portalSessionsService = m::mock();
        $portalSessionsService->shouldReceive('create')
            ->andReturnUsing(function (array $data) use ($capture, $portalRedirectUrl) {
                $capture->portalData = $data;

                return StripePortalSession::constructFrom([
                    'id' => 'bps_test_'.Str::random(14),
                    'url' => $portalRedirectUrl ?? ($data['return_url'] ?? '/'),
                ]);
            });

        $stripe = m::mock(StripeClient::class);
        $stripe->checkout = (object) ['sessions' => $sessionsService];
        $stripe->customers = $customersService;
        $stripe->billingPortal = (object) ['sessions' => $portalSessionsService];

        app()->bind(StripeClient::class, fn () => $stripe);

        return $capture;
    }
}

it('starts a Stripe Checkout session for the chosen plan and interval', function (string $plan, string $interval, string $expectedPriceId) {
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
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertNoJavaScriptErrors();

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

it('reuses the existing Stripe customer when starting another checkout', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $team->forceFill(['stripe_id' => 'cus_existing_team'])->save();

    $capture = fakeStripeForBilling(
        checkoutRedirectUrl: route('billing.success', ['team' => $team]).'?session_id=cs_test_xyz'
    );

    actingAs($user);

    visit('/app/'.$team->uuid.'/settings/subscription')
        ->click('[data-testid="checkout-pro-monthly"]')
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertNoJavaScriptErrors();

    expect($capture->customerData)->toBeNull();
    expect($capture->sessionData['customer'])->toBe('cus_existing_team');
});

it('shows a success notice after returning from a completed Stripe Checkout', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    actingAs($user);

    visit(route('billing.success', ['team' => $team]).'?session_id=cs_test_xyz')
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertSee('Thanks for subscribing!')
        ->assertNoJavaScriptErrors();
});

it('shows a cancellation notice after a cancelled Stripe Checkout', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    actingAs($user);

    visit(route('billing.cancel', ['team' => $team]))
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertSee('Payment was not completed')
        ->assertNoJavaScriptErrors();
});

it('redirects to the Stripe billing portal for an existing subscription', function (BillingInterval $interval) {
    $admin = User::factory()->create();
    $team = Team::factory()->onPlan(BillingPlan::Pro, $interval)->create(['user_id' => $admin->id]);
    $admin->update(['current_team_id' => $team->id]);

    $capture = fakeStripeForBilling(
        portalRedirectUrl: '/app/'.$team->uuid
    );

    actingAs($admin);

    visit('/app/'.$team->uuid.'/settings/subscription')
        ->click('[data-testid="manage-subscription-'.$interval->value.'"]')
        ->assertPathBeginsWith('/app/'.$team->uuid)
        ->assertNoJavaScriptErrors();

    expect($capture->portalData)
        ->not->toBeNull()
        ->and($capture->portalData['customer'])->toBe($team->stripe_id)
        ->and($capture->portalData['return_url'])->toContain('/app/'.$team->uuid);
})->with([
    'monthly' => [BillingInterval::Monthly],
    'yearly' => [BillingInterval::Yearly],
]);
