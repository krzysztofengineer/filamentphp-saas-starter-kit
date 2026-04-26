<?php

namespace App;

enum BillingPlan: string
{
    case Starter = 'starter';
    case Pro = 'pro';

    public function label(): string
    {
        return match ($this) {
            self::Starter => 'Starter',
            self::Pro => 'Pro',
        };
    }

    public function priceId(BillingInterval $interval): ?string
    {
        return config("billing.plans.{$this->value}.prices.{$interval->value}");
    }

    public function productId(): ?string
    {
        return config("billing.plans.{$this->value}.product");
    }

    public function maxTeamMemberships(): ?int
    {
        return null;
    }

    public static function freeMaxTeamMemberships(): int
    {
        return 1;
    }
}
