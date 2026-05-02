<?php

use App\Actions\TransferTeamOwnership;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertTrue;

it('marks the team creator as owner', function () {
    $creator = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $creator->id]);

    assertDatabaseHas('teams', ['id' => $team->id, 'user_id' => $creator->id]);
    assertTrue($team->creator->is($creator));
});

it('moves owner to the new admin after a transfer', function () {
    $oldOwner = User::factory()->create();
    $newOwner = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $oldOwner->id]);
    $team->members()->attach($oldOwner, ['role' => TeamRole::Administrator]);
    $team->members()->attach($newOwner, ['role' => TeamRole::Member]);

    (new TransferTeamOwnership)($team, $oldOwner, $newOwner);

    assertDatabaseHas('teams', ['id' => $team->id, 'user_id' => $newOwner->id]);
});
