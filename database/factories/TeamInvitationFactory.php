<?php

namespace Database\Factories;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamInvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'accepted_at' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['accepted_at' => now()]);
    }

    public function member(): static
    {
        return $this->state(fn () => ['role' => TeamRole::Member]);
    }

    public function administrator(): static
    {
        return $this->state(fn () => ['role' => TeamRole::Administrator]);
    }
}
