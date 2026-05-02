<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\DB;

class TransferTeamOwnership
{
    public function handle(Team $team, User $currentOwner, User $newOwner): void
    {
        DB::transaction(function () use ($team, $currentOwner, $newOwner) {
            $team->users()->updateExistingPivot($newOwner->id, ['role' => TeamRole::Administrator->value]);
            $team->users()->updateExistingPivot($currentOwner->id, ['role' => TeamRole::Member->value]);
            $team->update(['user_id' => $newOwner->id]);
        });
    }
}
