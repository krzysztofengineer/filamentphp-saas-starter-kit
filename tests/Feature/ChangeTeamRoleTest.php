<?php

use App\Actions\ChangeTeamRole;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\assertDatabaseHas;

it('changes a member role on the team', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $admin->id]);
    $team->members()->attach($admin, ['role' => TeamRole::Administrator]);
    $team->members()->attach($member, ['role' => TeamRole::Member]);

    (new ChangeTeamRole)($team, $member, TeamRole::Administrator);

    assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $member->id, 'role' => TeamRole::Administrator->value]);
});
