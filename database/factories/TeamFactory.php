<?php

namespace Database\Factories;

use App\Enums\BillingInterval;
use App\Enums\BillingPlan;
use App\Enums\TeamType;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->company(),
            'type' => TeamType::Company,
        ];
    }

    public function personal(): static
    {
        return $this->state(fn (array $attributes) => ['type' => TeamType::Personal]);
    }

    public function proPlan(BillingInterval $interval = BillingInterval::Yearly): static
    {
        return $this->onPlan(BillingPlan::Pro, $interval);
    }

    public function studioPlan(BillingInterval $interval = BillingInterval::Yearly): static
    {
        return $this->onPlan(BillingPlan::Studio, $interval);
    }

    public function onPlan(BillingPlan $plan, BillingInterval $interval): static
    {
        return $this->afterCreating(function (Team $team) use ($plan, $interval) {
            if ($team->stripe_id === null) {
                $team->forceFill(['stripe_id' => 'cus_test_'.Str::random(14)])->save();
            }

            $priceId = $plan->priceId($interval) ?: 'price_test_'.$plan->value.'_'.$interval->value;

            $subscription = $team->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => 'sub_test_'.Str::random(14),
                'stripe_status' => 'active',
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);

            $subscription->items()->create([
                'stripe_id' => 'si_test_'.Str::random(14),
                'stripe_product' => $plan->productId() ?? 'prod_test_'.$plan->value,
                'stripe_price' => $priceId,
                'quantity' => 1,
            ]);
        });
    }
}
