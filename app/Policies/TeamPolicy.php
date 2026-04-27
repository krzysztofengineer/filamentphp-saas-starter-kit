<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $user->teams()->whereKey($team->id)->exists();
    }

    public function update(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->first()?->pivot?->role !== TeamRole::Member;
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->canBeDeletedBy($user);
    }

    public function manageMembers(User $user, Team $team): bool
    {
        return $team->canBeManagedBy($user);
    }

    public function manageBilling(User $user, Team $team): bool
    {
        return $team->canBeManagedBy($user);
    }
}
