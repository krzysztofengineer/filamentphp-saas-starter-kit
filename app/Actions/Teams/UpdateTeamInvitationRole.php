<?php

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\TeamInvitation;

class UpdateTeamInvitationRole
{
    public function handle(TeamInvitation $invitation, TeamRole $role): void
    {
        $invitation->update(['role' => $role]);
    }
}
