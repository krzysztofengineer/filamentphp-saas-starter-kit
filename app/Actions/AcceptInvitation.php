<?php

namespace App\Actions;

use App\Models\Invitation;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\DB;

class AcceptInvitation
{
    public function __invoke(Invitation $invitation, User $user): void
    {
        DB::transaction(function () use ($invitation, $user) {
            $invitation->team
                ->users()
                ->syncWithoutDetaching([
                    $user->id => ['role' => TeamRole::Member->value],
                ]);

            $invitation->update(['accepted_at' => now()]);
        });
    }
}
