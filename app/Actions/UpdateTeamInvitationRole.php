<?php

namespace App\Actions;

use App\Models\TeamInvitation;
use App\TeamRole;

class UpdateTeamInvitationRole
{
    public function handle(TeamInvitation $invitation, TeamRole $role): void
    {
        $invitation->update(['role' => $role]);
    }
}
