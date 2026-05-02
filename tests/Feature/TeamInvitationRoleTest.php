<?php

use App\Actions\AcceptTeamInvitation;
use App\Models\TeamInvitation;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\assertDatabaseHas;

it('persists the role chosen at invite time', function () {
    $admin = User::factory()->create();
    $team = $admin->currentTeam;

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'user_id' => $admin->id,
        'email' => 'admin-to-be@example.com',
        'role' => TeamRole::Administrator->value,
    ]);

    assertDatabaseHas('team_invitations', ['id' => $invitation->id, 'role' => TeamRole::Administrator->value]);
});

it('updates the invitation role inline', function () {
    $admin = User::factory()->create();
    $team = $admin->currentTeam;

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'user_id' => $admin->id,
        'email' => 'pending@example.com',
        'role' => TeamRole::Member->value,
    ]);

    $invitation->update(['role' => TeamRole::Administrator->value]);

    assertDatabaseHas('team_invitations', ['id' => $invitation->id, 'role' => TeamRole::Administrator->value]);
});

it('uses the invitation role when accepted', function () {
    $admin = User::factory()->create();
    $team = $admin->currentTeam;

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'user_id' => $admin->id,
        'email' => 'invitee@example.com',
        'role' => TeamRole::Administrator->value,
    ]);

    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    (new AcceptTeamInvitation)->handle($invitation, $invitee);

    assertDatabaseHas('team_user', [
        'team_id' => $team->id,
        'user_id' => $invitee->id,
        'role' => TeamRole::Administrator->value,
    ]);
});
