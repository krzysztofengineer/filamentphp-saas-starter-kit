<?php

use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use App\TeamRole;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('grants view to all members and update/delete based on role', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $stranger = User::factory()->create();

    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Administrator]);
    $team->members()->attach($member, ['role' => TeamRole::Member]);

    $policy = new TeamPolicy;

    assertTrue($policy->view($admin, $team));
    assertTrue($policy->view($member, $team));
    assertFalse($policy->view($stranger, $team));

    assertTrue($policy->update($admin, $team));
    assertFalse($policy->update($member, $team));

    assertTrue($policy->delete($admin, $team));
    assertFalse($policy->delete($member, $team));
});
