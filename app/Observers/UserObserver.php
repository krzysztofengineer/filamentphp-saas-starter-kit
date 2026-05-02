<?php

namespace App\Observers;

use App\Models\User;
use App\TeamRole;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->current_team_id !== null) {
            return;
        }

        $team = $user->ownedTeams()->create([
            'name' => "{$user->name}'s Team",
        ]);

        $team->members()->attach($user, [
            'role' => TeamRole::Administrator,
        ]);

        $user->update(['current_team_id' => $team->id]);
    }
}
