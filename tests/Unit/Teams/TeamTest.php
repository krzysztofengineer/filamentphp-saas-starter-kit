<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

it('has an owner', function () {
    $user = Model::withoutEvents(function () {
        return User::factory()->create();
    });

    $team = Model::withoutEvents(function () use ($user) {
        return Team::factory()->create([
            'user_id' => $user->id,
        ]);
    });

    assertEquals($user->id, $team->owner->id);
    assertCount(1, $user->ownedTeams);
});

it('has members', function () {
    $user = Model::withoutEvents(function () {
        return User::factory()->create();
    });

    $team = Model::withoutEvents(function () use ($user) {
        return Team::factory()->create([
            'user_id' => $user->id,
        ]);
    });

    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    assertCount(1, $team->members);
    assertEquals(TeamRole::Administrator, $team->members[0]->pivot->role);
});
