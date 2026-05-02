<?php

namespace App\Observers;

use App\Models\TeamInvitation;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Notification;

class TeamInvitationObserver
{
    public function created(TeamInvitation $invitation): void
    {
        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitationNotification($invitation));
    }
}
