<?php

use App\Actions\AcceptInvitation;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;

it('attaches the user to the team as a member and marks the invitation accepted', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $admin->id]);
    $team->users()->attach($admin, ['role' => TeamRole::Administrator->value]);

    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'team_id' => $team->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'invitee@example.com',
    ]);

    (new AcceptInvitation)($invitation, $invitee);

    expect($team->fresh()->users()->whereKey($invitee->id)->exists())->toBeTrue();
    expect($invitation->fresh()->isAccepted())->toBeTrue();
    expect($team->fresh()->roleFor($invitee))->toBe(TeamRole::Member);
});
