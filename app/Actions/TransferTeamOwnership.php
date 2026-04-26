<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\DB;

class TransferTeamOwnership
{
    public function __invoke(Team $team, User $currentOwner, User $newOwner): void
    {
        DB::transaction(function () use ($team, $currentOwner, $newOwner) {
            $team->users()->updateExistingPivot($currentOwner->id, ['role' => TeamRole::Member->value]);
            $team->users()->updateExistingPivot($newOwner->id, ['role' => TeamRole::Owner->value]);
        });
    }
}
