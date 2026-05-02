<?php

use App\Actions\TransferTeamOwnership;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\assertDatabaseHas;

it('promotes the new admin, demotes the previous, and moves team ownership', function () {
    $oldOwner = User::factory()->create();
    $newOwner = User::factory()->create();

    $team = Team::factory()->create(['user_id' => $oldOwner->id]);
    $team->members()->attach($oldOwner, ['role' => TeamRole::Administrator]);
    $team->members()->attach($newOwner, ['role' => TeamRole::Member]);

    (new TransferTeamOwnership)->handle($team, $oldOwner, $newOwner);

    assertDatabaseHas('teams', ['id' => $team->id, 'user_id' => $newOwner->id]);
    assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $oldOwner->id, 'role' => TeamRole::Member->value]);
    assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $newOwner->id, 'role' => TeamRole::Administrator->value]);
});
