<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransferTeamOwnership
{
    public function handle(Team $team, User $currentOwner, User $newOwner): void
    {
        if ($team->isPersonal()) {
            throw new RuntimeException('Personal teams cannot be transferred.');
        }

        DB::transaction(function () use ($team, $currentOwner, $newOwner) {
            $team->users()->updateExistingPivot($newOwner->id, ['role' => TeamRole::Administrator->value]);
            $team->users()->updateExistingPivot($currentOwner->id, ['role' => TeamRole::Member->value]);
            $team->update(['user_id' => $newOwner->id]);
        });
    }
}
