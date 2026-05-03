<?php

namespace App\Observers;

use App\Actions\AttachTeamOwner;
use App\Models\Team;
use Illuminate\Support\Str;

class TeamObserver
{
    public function creating(Team $team): void
    {
        if (empty($team->uuid)) {
            $team->uuid = (string) Str::uuid();
        }
    }

    public function created(Team $team): void
    {
        (new AttachTeamOwner)->handle($team);
    }
}
