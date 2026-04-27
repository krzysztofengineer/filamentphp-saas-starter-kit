<?php

namespace Database\Factories;

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
            $user->teams()->attach($team, ['role' => TeamRole::Administrator->value]);
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
}
