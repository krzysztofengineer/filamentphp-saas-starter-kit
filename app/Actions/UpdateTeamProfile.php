<?php

namespace App\Actions;

use App\Models\Team;
use Illuminate\Support\Facades\Storage;

class UpdateTeamProfile
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Team $team, array $attributes): void
    {
        if (array_key_exists('logo', $attributes)) {
            $new = $attributes['logo'];

            if (filled($team->logo) && $team->logo !== $new) {
                Storage::disk('team-logos')->delete($team->logo);
            }
        }

        $team->update($attributes);
    }
}
