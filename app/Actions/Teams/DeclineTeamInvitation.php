<?php

namespace App\Actions\Teams;

use App\Models\TeamInvitation;

class DeclineTeamInvitation
{
    public function handle(TeamInvitation $invitation): void
    {
        $invitation->delete();
    }
}
