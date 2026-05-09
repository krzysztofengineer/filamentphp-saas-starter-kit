<?php

namespace App\Enums;

enum BillingPlan: string
{
    case Pro = 'pro';
    case Studio = 'studio';

    public function label(): string
    {
        return match ($this) {
            self::Pro => 'Pro',
            self::Studio => 'Studio',
        };
    }

    public function priceId(BillingInterval $interval): ?string
    {
        return config("billing.plans.{$this->value}.prices.{$interval->value}.stripe_id");
    }

    public function productId(): ?string
    {
        return config("billing.plans.{$this->value}.product");
    }
}
