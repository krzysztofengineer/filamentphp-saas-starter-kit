<?php

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AcceptTeamInvitation
{
    public function handle(TeamInvitation $invitation, User $user): void
    {
        DB::transaction(function () use ($invitation, $user) {
            $role = $invitation->role ?? TeamRole::Member;

            $invitation->team
                ->users()
                ->syncWithoutDetaching([
                    $user->id => ['role' => $role->value],
                ]);

            $user->update(['current_team_id' => $invitation->team_id]);

            $invitation->delete();
        });
    }
}
