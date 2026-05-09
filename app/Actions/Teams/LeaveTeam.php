<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaveTeam
{
    public function handle(Team $team, User $member): void
    {
        DB::transaction(function () use ($team, $member): void {
            $team->users()->detach($member->id);

            if ($member->current_team_id === $team->id) {
                $member->update(['current_team_id' => null]);
            }
        });
    }
}
