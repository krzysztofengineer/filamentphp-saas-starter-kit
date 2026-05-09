<?php

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use RuntimeException;

class InviteToTeam
{
    public function handle(Team $team, User $invitedBy, string $email, TeamRole $role): TeamInvitation
    {
        if ($team->isPersonal()) {
            throw new RuntimeException('Personal teams cannot have additional members.');
        }

        return TeamInvitation::create([
            'team_id' => $team->id,
            'user_id' => $invitedBy->id,
            'email' => $email,
            'role' => $role->value,
        ]);
    }
}
