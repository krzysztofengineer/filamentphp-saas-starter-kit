<?php

use App\Actions\AcceptTeamInvitation;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertTrue;

it('attaches the user to the team and removes the invitation', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $admin->id]);
    $team->users()->attach($admin, ['role' => TeamRole::Administrator->value]);

    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = TeamInvitation::create([
        'team_id' => $team->id,
        'user_id' => $admin->id,
        'email' => 'invitee@example.com',
    ]);

    (new AcceptTeamInvitation)($invitation, $invitee);

    assertTrue($team->fresh()->members->contains($invitee));
    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});
