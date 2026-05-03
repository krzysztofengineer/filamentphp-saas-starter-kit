<?php

namespace App\Actions;

use App\Models\Team;
use App\TeamRole;

class AttachTeamOwner
{
    public function handle(Team $team): void
    {
        if ($team->user_id === null) {
            return;
        }

        $team->members()->syncWithoutDetaching([
            $team->user_id => ['role' => TeamRole::Administrator->value],
        ]);
    }
}
