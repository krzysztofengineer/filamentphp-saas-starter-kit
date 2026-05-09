<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;

class SaveUserCurrentTeam
{
    public function handle(User $user, Team $team): void
    {
        if ($user->current_team_id === $team->id) {
            return;
        }

        $user->update(['current_team_id' => $team->id]);
        $user->unsetRelation('currentTeam');
    }
}
