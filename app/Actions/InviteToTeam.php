<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\TeamRole;

class InviteToTeam
{
    public function handle(Team $team, User $invitedBy, string $email, TeamRole $role): TeamInvitation
    {
        return TeamInvitation::create([
            'team_id' => $team->id,
            'user_id' => $invitedBy->id,
            'email' => $email,
            'role' => $role->value,
        ]);
    }
}
