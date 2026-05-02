<?php

namespace App\Actions;

use App\Models\TeamInvitation;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\DB;

class AcceptTeamInvitation
{
    public function __invoke(TeamInvitation $invitation, User $user): void
    {
        DB::transaction(function () use ($invitation, $user) {
            $role = $invitation->role ?? TeamRole::Member;

            $invitation->team
                ->users()
                ->syncWithoutDetaching([
                    $user->id => ['role' => $role->value],
                ]);

            $invitation->delete();
        });
    }
}
