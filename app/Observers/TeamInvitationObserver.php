<?php

namespace App\Observers;

use App\Actions\SendTeamInvitationNotification;
use App\Models\TeamInvitation;

class TeamInvitationObserver
{
    public function created(TeamInvitation $invitation): void
    {
        (new SendTeamInvitationNotification)->handle($invitation);
    }
}
