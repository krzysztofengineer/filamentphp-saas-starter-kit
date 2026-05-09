<?php

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

class ChangeTeamRole
{
    public function handle(Team $team, User $user, TeamRole $role): void
    {
        $team->users()->updateExistingPivot($user->id, ['role' => $role->value]);
    }
}
