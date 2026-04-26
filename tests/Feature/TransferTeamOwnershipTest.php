<?php

use App\Actions\TransferTeamOwnership;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

it('swaps the owner and member roles between two users', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $owner->id]);
    $team->users()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->users()->attach($member, ['role' => TeamRole::Member->value]);

    (new TransferTeamOwnership)($team, $owner, $member);

    expect($team->fresh()->roleFor($owner))->toBe(TeamRole::Member);
    expect($team->fresh()->roleFor($member))->toBe(TeamRole::Owner);
});
