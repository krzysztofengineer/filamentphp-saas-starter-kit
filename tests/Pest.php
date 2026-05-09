<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery as m;
use Stripe\BillingPortal\Session as StripePortalSession;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Customer as StripeCustomer;
use Stripe\StripeClient;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class);

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
