<?php

use App\Actions\TransferTeamOwnership;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

it('promotes the new admin, demotes the previous, and moves team ownership', function () {
    $oldOwner = User::factory()->create();
    $newOwner = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $oldOwner->id]);
    $team->users()->attach($oldOwner, ['role' => TeamRole::Administrator->value]);
    $team->users()->attach($newOwner, ['role' => TeamRole::Member->value]);

    (new TransferTeamOwnership)($team, $oldOwner, $newOwner);

    $team->refresh();
    expect($team->user_id)->toBe($newOwner->id);
    expect($team->roleFor($oldOwner))->toBe(TeamRole::Member);
    expect($team->roleFor($newOwner))->toBe(TeamRole::Administrator);
});
