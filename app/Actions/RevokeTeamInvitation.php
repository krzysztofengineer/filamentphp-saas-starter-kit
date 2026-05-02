<?php

namespace App\Actions;

use App\Models\TeamInvitation;

class RevokeTeamInvitation
{
    public function handle(TeamInvitation $invitation): void
    {
        $invitation->delete();
    }
}
