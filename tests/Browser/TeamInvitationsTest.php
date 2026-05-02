<?php

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use App\TeamRole;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertEquals;

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
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit-button')
        ->assertNotPresent('@invite-submit-button')
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
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit-button')
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
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', $user->email)
        ->click('@invite-submit-button')
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
        ->click('@team-members')
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
        ->click('@team-members')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="revoke-invitation-button"]')
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
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', 'test@example.com')
        ->click('@invite-submit-button')
        ->assertNotPresent('@invite-submit-button');

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
                && str_contains($rendered, route('filament.app.auth.login'))
                && str_contains($rendered, route('filament.app.auth.register'));
        },
    );
});
