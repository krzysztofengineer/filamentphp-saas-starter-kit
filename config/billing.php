<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Map subscription plans to Stripe price IDs. Each plan has both a
    | monthly and a yearly billing variant. Prices are managed in Stripe
    | (Managed Payments) and pasted into your .env per environment
    | (sandbox / prod).
    */

    'plans' => [
        'starter' => [
            'label' => 'Starter',
            'product' => env('STRIPE_PRODUCT_STARTER'),
            'prices' => [
                'monthly' => env('STRIPE_PRICE_STARTER_MONTHLY'),
                'yearly' => env('STRIPE_PRICE_STARTER_YEARLY'),
            ],
        ],
        'pro' => [
            'label' => 'Pro',
            'product' => env('STRIPE_PRODUCT_PRO'),
            'prices' => [
                'monthly' => env('STRIPE_PRICE_PRO_MONTHLY'),
                'yearly' => env('STRIPE_PRICE_PRO_YEARLY'),
            ],
        ],
    ],
];
