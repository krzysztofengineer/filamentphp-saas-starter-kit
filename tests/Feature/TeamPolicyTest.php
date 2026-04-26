<?php

use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use App\TeamRole;

it('grants view to members and update/delete only to owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $stranger = User::factory()->create();

    $team = Team::factory()->create();
    $team->users()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->users()->attach($member, ['role' => TeamRole::Member->value]);

    $policy = new TeamPolicy;

    expect($policy->view($owner, $team))->toBeTrue();
    expect($policy->view($member, $team))->toBeTrue();
    expect($policy->view($stranger, $team))->toBeFalse();

    expect($policy->update($owner, $team))->toBeTrue();
    expect($policy->update($member, $team))->toBeFalse();

    expect($policy->delete($owner, $team))->toBeTrue();
    expect($policy->delete($member, $team))->toBeFalse();
});
