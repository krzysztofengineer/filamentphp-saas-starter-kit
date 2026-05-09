<?php

namespace App\Actions\Teams;

use App\Enums\TeamType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTeamForUser
{
    public function handle(User $owner, string $name, TeamType $type = TeamType::Company): Team
    {
        return DB::transaction(function () use ($owner, $name, $type): Team {
            $team = Team::create([
                'name' => $name,
                'user_id' => $owner->id,
                'type' => $type,
            ]);

            $owner->update(['current_team_id' => $team->id]);

            return $team;
        });
    }
}
