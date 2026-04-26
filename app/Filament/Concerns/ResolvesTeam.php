<?php

namespace App\Filament\Concerns;

use App\Models\Team;
use Filament\Facades\Filament;
use Livewire\Attributes\Locked;

trait ResolvesTeam
{
    #[Locked]
    public ?int $teamId = null;

    public function resolvedTeam(): ?Team
    {
        if ($this->teamId !== null) {
            return Team::find($this->teamId);
        }

        return Filament::getTenant();
    }

    public function resolvedTeamId(): ?int
    {
        return $this->resolvedTeam()?->id;
    }
}
