<?php

use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use App\TeamRole;

it('grants view to all members and update/delete based on role', function () {
    $admin = User::factory()->create();
    $manager = User::factory()->create();
    $member = User::factory()->create();
    $stranger = User::factory()->create();

    $team = Team::factory()->create();
    $team->users()->attach($admin, ['role' => TeamRole::Administrator->value]);
    $team->users()->attach($manager, ['role' => TeamRole::Manager->value]);
    $team->users()->attach($member, ['role' => TeamRole::Member->value]);

    $policy = new TeamPolicy;

    expect($policy->view($admin, $team))->toBeTrue();
    expect($policy->view($manager, $team))->toBeTrue();
    expect($policy->view($member, $team))->toBeTrue();
    expect($policy->view($stranger, $team))->toBeFalse();

    expect($policy->update($admin, $team))->toBeTrue();
    expect($policy->update($manager, $team))->toBeTrue();
    expect($policy->update($member, $team))->toBeFalse();

    expect($policy->delete($admin, $team))->toBeTrue();
    expect($policy->delete($manager, $team))->toBeFalse();
    expect($policy->delete($member, $team))->toBeFalse();
});
