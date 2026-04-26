<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $team = Team::factory()->create([
            'name' => 'Test Team',
            'user_id' => $user->id,
        ]);

        $user->teams()->attach($team, ['role' => TeamRole::Owner->value]);
        $user->update(['current_team_id' => $team->id]);
    }
}
