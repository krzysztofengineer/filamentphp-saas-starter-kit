<?php

namespace App\Policies;

use App\Models\TeamInvitation;
use App\Models\User;

class TeamInvitationPolicy
{
    public function create(User $user, TeamInvitation $invitation): bool
    {
        return $invitation->team->canBeManagedBy($user);
    }

    public function delete(User $user, TeamInvitation $invitation): bool
    {
        return $invitation->team->canBeManagedBy($user);
    }

    public function accept(User $user, TeamInvitation $invitation): bool
    {
        return ! $invitation->isAccepted()
            && strtolower($user->email) === strtolower($invitation->email);
    }
}
