<?php

namespace App\Observers;

use App\Actions\Teams\CreateTeamForUser;
use App\Enums\TeamType;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->current_team_id !== null) {
            return;
        }

        (new CreateTeamForUser)->handle($user, "{$user->name}'s Team", TeamType::Personal);
    }
}
