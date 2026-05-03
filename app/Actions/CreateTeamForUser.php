<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTeamForUser
{
    public function handle(User $owner, string $name): Team
    {
        return DB::transaction(function () use ($owner, $name): Team {
            $team = Team::create([
                'name' => $name,
                'user_id' => $owner->id,
            ]);

            $owner->update(['current_team_id' => $team->id]);

            return $team;
        });
    }
}
