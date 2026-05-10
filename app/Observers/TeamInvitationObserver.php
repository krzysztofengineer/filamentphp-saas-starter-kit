<?php

namespace App\Observers;

use App\Actions\Teams\SendTeamInvitationNotification;
use App\Models\TeamInvitation;

class TeamInvitationObserver
{
    public function creating(TeamInvitation $invitation): void
    {
        if (empty($invitation->token)) {
            $invitation->token = TeamInvitation::generateToken();
        }

        $invitation->email = strtolower($invitation->email);
    }

    public function created(TeamInvitation $invitation): void
    {
        (new SendTeamInvitationNotification)->handle($invitation);
    }
}
