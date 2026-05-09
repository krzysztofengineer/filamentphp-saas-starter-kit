<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    Notification::fake();
});

it('invites new members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('@invite')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit')
        ->assertNotPresent('@invite-submit')
        ->assertSee('test@example.com');

    assertDatabaseCount('team_invitations', 1);
    assertDatabaseHas('team_invitations', [
        'team_id' => $team->id,
        'email' => 'test@example.com',
    ]);
});

it('cannot invite the same email twice', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    TeamInvitation::factory()->for($team)->create(['email' => 'test@example.com']);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('@invite')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit')
        ->assertSee('An invitation for that email already exists');

    assertDatabaseCount('team_invitations', 1);
});

it('cannot invite existing members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('@invite')
        ->fill('@invite-email', $user->email)
        ->click('@invite-submit')
        ->assertSee('That user is already a member');
});

it('updates invitation role', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $invitation = TeamInvitation::factory()->for($team)->member()->create(['email' => 'test@example.com']);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('[data-testid="invitation-role-select"]')
        ->click('.fi-dropdown-panel:visible [data-value="administrator"]')
        ->assertSee('Role updated');

    assertEquals(TeamRole::Administrator, $invitation->fresh()->role);
});

it('removes the invitation', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $invitation = TeamInvitation::factory()->for($team)->create(['email' => 'test@example.com']);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="revoke-invitation"]')
        ->click('[data-testid="revoke-invitation-confirm"]')
        ->assertSee('Invitation revoked');

    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});

it('sends the invitation notification', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id, 'name' => 'Alice']);
    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-settings')
        ->click('.fi-sidebar-item-btn[href*="/settings/members"]')
        ->click('@invite')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit')
        ->assertNotPresent('@invite-submit');

    Notification::assertSentOnDemand(
        TeamInvitationNotification::class,
        function (TeamInvitationNotification $notification, array $channels, object $notifiable) use ($team, $user): bool {
            if ($notifiable->routeNotificationFor('mail') !== 'test@example.com') {
                return false;
            }

            $mail = $notification->toMail($notifiable);
            $rendered = (string) $mail->render();

            return str_contains((string) $mail->subject, $team->name)
                && str_contains($rendered, $user->name)
                && str_contains($rendered, $team->name)
                && str_contains($rendered, route('team-invitations.show'));
        },
    );
});

it('accepts the invitation for the existing user', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Acme', 'user_id' => $admin->id]);

    $invitee = User::factory()->create(['email' => 'invitee@example.com']);
    $inviteeTeam = $invitee->ownedTeams()->first();

    $invitation = TeamInvitation::factory()->for($team)->administrator()->create([
        'email' => 'invitee@example.com',
        'user_id' => $admin->id,
    ]);

    visit('/team-invitations')
        ->fill('@login-email', 'invitee@example.com')
        ->fill('@login-password', 'password')
        ->click('@login-submit')
        ->assertPathIs("/app/{$inviteeTeam->uuid}/account/team-invitations")
        ->assertSee($team->name)
        ->click('[data-testid="invitation-accept"]')
        ->assertSee('Joined the team');

    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
    assertTrue($team->fresh()->members->contains($invitee));
    assertDatabaseHas('team_user', [
        'team_id' => $team->id,
        'user_id' => $invitee->id,
        'role' => TeamRole::Administrator->value,
    ]);
});

it('accepts the invitation for the new user', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Acme', 'user_id' => $admin->id]);

    $invitation = TeamInvitation::factory()->for($team)->member()->create([
        'email' => 'newbie@example.com',
        'user_id' => $admin->id,
    ]);

    visit('/team-invitations')
        ->click('a[href*="register"]')
        ->assertPathIs('/app/register')
        ->fill('@register-name', 'Newbie')
        ->fill('@register-email', 'newbie@example.com')
        ->fill('@register-password', 'password')
        ->fill('@register-password-confirm', 'password')
        ->click('@register-terms')
        ->click('@register-privacy')
        ->click('@register-submit')
        ->assertNotPresent('@register-submit')
        ->assertPathIs('/app/email-verification/prompt');

    $user = User::query()->where('email', 'newbie@example.com')->firstOrFail();
    $verifyUrl = URL::temporarySignedRoute(
        'filament.app.auth.email-verification.verify',
        now()->addHour(),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $tenant = $user->ownedTeams()->first();

    visit($verifyUrl)
        ->click('button.fi-user-menu-trigger')
        ->click('@user-menu-team-invitations')
        ->assertPathIs("/app/{$tenant->uuid}/account/team-invitations")
        ->assertSee($team->name)
        ->click('[data-testid="invitation-accept"]')
        ->assertSee('Joined the team');

    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
    assertNotNull($user->fresh()->email_verified_at);
    assertTrue($team->fresh()->members->contains($user));
});

it('does not show other members invitations', function () {
    $invitee = User::factory()->create();

    $otherTeam = Team::factory()->create();
    TeamInvitation::factory()->for($otherTeam)->create(['email' => 'somebody-else@example.com']);

    actingAs($invitee);

    visit("/app/{$invitee->ownedTeams()->first()->uuid}/account/team-invitations")
        ->assertSee('No invitations');
});

it('shows invitations from all teams', function () {
    $invitee = User::factory()->create();

    $teamA = Team::factory()->create(['name' => 'Alpha']);
    $teamB = Team::factory()->create(['name' => 'Beta']);

    TeamInvitation::factory()->for($teamA)->create(['email' => $invitee->email]);
    TeamInvitation::factory()->for($teamB)->create(['email' => $invitee->email]);

    actingAs($invitee);

    visit("/app/{$invitee->ownedTeams()->first()->uuid}/account/team-invitations")
        ->assertSee('Alpha')
        ->assertSee('Beta');
});

it('declines invitations', function () {
    $invitee = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Acme']);

    $invitation = TeamInvitation::factory()->for($team)->member()->create(['email' => $invitee->email]);

    actingAs($invitee);

    visit("/app/{$invitee->ownedTeams()->first()->uuid}/account/team-invitations")
        ->assertSee($team->name)
        ->click('[data-testid="invitation-decline"]')
        ->click('[data-testid="invitation-decline-confirm"]')
        ->assertSee('Invitation declined');

    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});
