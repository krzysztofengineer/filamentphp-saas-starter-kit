<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $user->teams()->whereKey($team->id)->exists();
    }

    public function update(User $user, Team $team): bool
    {
        return $team->administrators()->whereKey($user->id)->exists();
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->administrators()->whereKey($user->id)->exists();
    }

    public function manageMembers(User $user, Team $team): bool
    {
        return $team->administrators()->whereKey($user->id)->exists();
    }

    public function manageBilling(User $user, Team $team): bool
    {
        return $team->administrators()->whereKey($user->id)->exists();
    }
}
