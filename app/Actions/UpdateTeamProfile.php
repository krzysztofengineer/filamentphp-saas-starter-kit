<?php

namespace App\Actions;

use App\Models\Team;

class UpdateTeamProfile
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Team $team, array $attributes): void
    {
        $team->update($attributes);
    }
}
