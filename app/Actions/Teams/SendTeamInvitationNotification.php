<?php

namespace App\Actions\Teams;

use App\Models\TeamInvitation;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Notification;

class SendTeamInvitationNotification
{
    public function handle(TeamInvitation $invitation): void
    {
        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitationNotification($invitation));
    }
}
