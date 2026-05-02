<?php

use App\Actions\AcceptTeamInvitation;
use App\Models\TeamInvitation;
use App\Models\User;
use App\TeamRole;

it('persists the role chosen at invite time', function () {
    $admin = User::factory()->withTeam()->create();
    $team = $admin->teams()->first();

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'mgr@example.com',
        'role' => TeamRole::Manager->value,
    ]);

    expect($invitation->fresh()->role)->toBe(TeamRole::Manager);
});

it('updates the invitation role inline', function () {
    $admin = User::factory()->withTeam()->create();
    $team = $admin->teams()->first();

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'pending@example.com',
        'role' => TeamRole::Member->value,
    ]);

    $invitation->update(['role' => TeamRole::Administrator->value]);

    expect($invitation->fresh()->role)->toBe(TeamRole::Administrator);
});

it('uses the invitation role when accepted', function () {
    $admin = User::factory()->withTeam()->create();
    $team = $admin->teams()->first();

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'invitee@example.com',
        'role' => TeamRole::Manager->value,
    ]);

    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    (new AcceptTeamInvitation)($invitation, $invitee);

    expect($team->fresh()->roleFor($invitee))->toBe(TeamRole::Manager);
});
