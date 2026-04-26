<?php

namespace Database\Factories;

use App\BillingInterval;
use App\BillingPlan;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function withTeam(): static
    {
        return $this->afterCreating(function (User $user) {
            $team = Team::factory()->create(['user_id' => $user->id]);
            $user->teams()->attach($team, ['role' => TeamRole::Owner->value]);
            $user->current_team_id = $team->id;
            $user->save();
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function starterPlan(BillingInterval $interval = BillingInterval::Yearly): static
    {
        return $this->onPlan(BillingPlan::Starter, $interval);
    }

    public function proPlan(BillingInterval $interval = BillingInterval::Yearly): static
    {
        return $this->onPlan(BillingPlan::Pro, $interval);
    }

    public function onPlan(BillingPlan $plan, BillingInterval $interval): static
    {
        return $this->afterCreating(function (User $user) use ($plan, $interval) {
            if ($user->stripe_id === null) {
                $user->forceFill(['stripe_id' => 'cus_test_'.Str::random(14)])->save();
            }

            $priceId = $plan->priceId($interval) ?? 'price_test_'.$plan->value.'_'.$interval->value;

            $subscription = $user->subscriptions()->create([
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
