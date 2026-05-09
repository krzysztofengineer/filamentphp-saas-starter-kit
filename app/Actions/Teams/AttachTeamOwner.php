<?php

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\Team;

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
