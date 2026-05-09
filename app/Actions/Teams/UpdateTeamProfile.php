<?php

namespace App\Actions\Teams;

use App\Models\Team;
use Illuminate\Support\Facades\Storage;

class UpdateTeamProfile
{
    public function handle(Team $team, array $attributes): void
    {
        if (array_key_exists('logo', $attributes) && filled($team->logo) && $team->logo !== $attributes['logo']) {
            Storage::disk('team-logos')->delete($team->logo);
        }

        $team->update($attributes);
    }
}
