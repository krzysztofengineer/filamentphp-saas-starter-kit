<?php

use App\Actions\TransferTeamOwnership;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

it('marks the team creator as owner', function () {
    $creator = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $creator->id]);

    expect($team->user_id)->toBe($creator->id);
    expect($team->creator->is($creator))->toBeTrue();
});

it('moves owner to the new admin after a transfer', function () {
    $oldOwner = User::factory()->create();
    $newOwner = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $oldOwner->id]);
    $team->users()->attach($oldOwner, ['role' => TeamRole::Administrator->value]);
    $team->users()->attach($newOwner, ['role' => TeamRole::Member->value]);

    (new TransferTeamOwnership)($team, $oldOwner, $newOwner);

    expect($team->fresh()->user_id)->toBe($newOwner->id);
});
