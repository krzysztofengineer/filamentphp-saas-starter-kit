<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function create(User $user, Invitation $invitation): bool
    {
        return $invitation->team->canBeManagedBy($user);
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $invitation->team->canBeManagedBy($user);
    }

    public function accept(User $user, Invitation $invitation): bool
    {
        return ! $invitation->isAccepted()
            && strtolower($user->email) === strtolower($invitation->email);
    }
}
