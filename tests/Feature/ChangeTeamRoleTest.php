<?php

use App\Actions\ChangeTeamRole;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

it('changes a member role on the team', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $admin->id]);
    $team->users()->attach($admin, ['role' => TeamRole::Administrator->value]);
    $team->users()->attach($member, ['role' => TeamRole::Member->value]);

    (new ChangeTeamRole)($team, $member, TeamRole::Manager);

    expect($team->fresh()->roleFor($member))->toBe(TeamRole::Manager);
});
