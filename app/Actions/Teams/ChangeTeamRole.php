<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;

class ChangeTeamRole
{
    public function handle(Team $team, User $user, TeamRole $role): void
    {
        $team->users()->updateExistingPivot($user->id, ['role' => $role->value]);
    }
}
