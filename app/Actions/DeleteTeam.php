<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteTeam
{
    public function handle(Team $team, User $actor): ?Team
    {
        return DB::transaction(function () use ($team, $actor): ?Team {
            $nextTeam = $actor->teams()
                ->where('teams.id', '!=', $team->id)
                ->orderBy('teams.id')
                ->first();

            $team->delete();

            if ($nextTeam !== null) {
                $actor->update(['current_team_id' => $nextTeam->id]);
            }

            return $nextTeam;
        });
    }
}
