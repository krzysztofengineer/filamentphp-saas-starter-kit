<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Plans are the single source of truth for both the marketing landing
    | page (`#pricing` section) and the in-app Subscription settings page.
    | Stripe price IDs are kept in env per environment (sandbox/prod).
    |
    | Each plan exposes:
    |   - name, badge, description, features    →  marketing copy
    |   - prices.monthly|yearly                 →  amount (minor units),
    |     label, period, savings, stripe_id
    |   - product                               →  Stripe product id
    |   - highlighted                           →  visual emphasis flag
    |   - is_free                               →  no Stripe checkout
    */

    'plans' => [
        'free' => [
            'name' => 'Free',
            'badge' => null,
            'description' => 'For projects under <strong>$1k/month revenue</strong>.',
            'features' => [
                'Full source code',
                'Commercial use up to $1k MRR',
                'Community support',
                'Updates while on the free tier',
            ],
            'prices' => [
                'monthly' => [
                    'amount' => 0,
                    'label' => '$0',
                    'period' => 'forever',
                    'stripe_id' => null,
                ],
                'yearly' => [
                    'amount' => 0,
                    'label' => '$0',
                    'period' => 'forever',
                    'stripe_id' => null,
                ],
            ],
            'product' => null,
            'highlighted' => false,
            'is_free' => true,
        ],

        'pro' => [
            'name' => 'Pro',
            'badge' => 'Most popular',
            'description' => 'For projects up to <strong>$10k/month revenue</strong>.',
            'features' => [
                'Everything in Free, plus:',
                'Commercial use up to $10k MRR',
                'Priority issue triage',
                'Private Discord with the maintainers',
                'Figma source for the landing page',
            ],
            'prices' => [
                'monthly' => [
                    'amount' => 2900,
                    'label' => '$29',
                    'period' => 'per month',
                    'stripe_id' => env('STRIPE_PRICE_PRO_MONTHLY'),
                ],
                'yearly' => [
                    'amount' => 27800,
                    'label' => '$278',
                    'period' => 'per year',
                    'savings' => 'Save 20%',
                    'stripe_id' => env('STRIPE_PRICE_PRO_YEARLY'),
                ],
            ],
            'product' => env('STRIPE_PRODUCT_PRO'),
            'highlighted' => true,
            'is_free' => false,
        ],

        'studio' => [
            'name' => 'Studio',
            'badge' => null,
            'description' => 'For projects past <strong>$10k/month revenue</strong> and agencies.',
            'features' => [
                'Everything in Pro, plus:',
                'No revenue cap',
                '<strong>Unlimited projects</strong> — use it on every client build',
                '1-on-1 onboarding call',
            ],
            'prices' => [
                'monthly' => [
                    'amount' => 7900,
                    'label' => '$79',
                    'period' => 'per month',
                    'stripe_id' => env('STRIPE_PRICE_STUDIO_MONTHLY'),
                ],
                'yearly' => [
                    'amount' => 75800,
                    'label' => '$758',
                    'period' => 'per year',
                    'savings' => 'Save 20%',
                    'stripe_id' => env('STRIPE_PRICE_STUDIO_YEARLY'),
                ],
            ],
            'product' => env('STRIPE_PRODUCT_STUDIO'),
            'highlighted' => false,
            'is_free' => false,
        ],
    ],
];
