<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
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

            $team->users()->attach($owner, ['role' => TeamRole::Administrator->value]);

            $owner->update(['current_team_id' => $team->id]);

            return $team;
        });
    }
}
