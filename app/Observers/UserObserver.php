<?php

namespace App\Observers;

use App\Models\User;
use App\TeamRole;

class UserObserver
{
    public function created(User $user): void
    {
        $team = $user->ownedTeams()->create([
            'name' => "{$user->name}'s Team",
        ]);

        $team->members()->attach($user, [
            'role' => TeamRole::Administrator,
        ]);
    }
}
