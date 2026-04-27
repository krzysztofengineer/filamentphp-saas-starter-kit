<?php

use App\Models\Invitation;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('lets a team admin send an invitation as a Member by default', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->click('[data-testid="invite-button"]')
        ->fill('[data-testid="invite-email"]', 'invitee@example.com')
        ->click('[data-testid="invite-submit-button"]')
        ->assertSee('Invitation sent')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('invitations', [
        'team_id' => $tenant->id,
        'email' => 'invitee@example.com',
        'role' => TeamRole::Member->value,
        'accepted_at' => null,
    ]);
});

it('renders the inline role select on each pending invitation row', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();

    Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'mgr-pending@example.com',
        'role' => TeamRole::Manager->value,
    ]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('mgr-pending@example.com')
        ->assertPresent('[data-testid="invitation-role-select"]')
        ->assertNoJavaScriptErrors();
});

it('renders the invite modal with the role picker', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->click('[data-testid="invite-button"]')
        ->assertPresent('[data-testid="invite-email"]')
        ->assertPresent('[data-testid="invite-role"]')
        ->assertSee('Role');
});

it('lets the invitee accept an invitation and joins them with the invited role', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();

    Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'invitee@example.com',
        'role' => TeamRole::Manager->value,
    ]);

    $invitee = User::factory()->withTeam()->create([
        'email' => 'invitee@example.com',
    ]);
    actingAs($invitee);

    visit('/app/'.$invitee->teams()->first()->uuid)
        ->assertSee($tenant->name)
        ->click('[data-testid="invitation-accept"]')
        ->assertPathIs('/app/'.$tenant->uuid);

    assertDatabaseHas('team_user', [
        'team_id' => $tenant->id,
        'user_id' => $invitee->id,
        'role' => TeamRole::Manager->value,
    ]);
});

it('lets the admin revoke a pending invitation', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();

    $invitation = Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'invitee@example.com',
        'role' => TeamRole::Member->value,
    ]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('invitee@example.com')
        ->click('button[aria-label="Actions"]')
        ->click('[data-testid="revoke-invitation-button"]')
        ->click('[data-testid="revoke-invitation-confirm"]')
        ->assertSee('Invitation revoked')
        ->assertNoJavaScriptErrors();

    assertDatabaseMissing('invitations', ['id' => $invitation->id]);
});

it('blocks duplicate invitations for the same email', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();

    Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'dup@example.com',
        'role' => TeamRole::Member->value,
    ]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->click('[data-testid="invite-button"]')
        ->fill('[data-testid="invite-email"]', 'dup@example.com')
        ->click('[data-testid="invite-submit-button"]')
        ->assertSee('An invitation for that email already exists');
});

it('blocks invitations to existing team members', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create(['email' => 'already@example.com']);
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);
    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->click('[data-testid="invite-button"]')
        ->fill('[data-testid="invite-email"]', 'already@example.com')
        ->click('[data-testid="invite-submit-button"]')
        ->assertSee('That user is already a member');
});

it('lets the user decline a pending invitation from the dashboard', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();

    $invitation = Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $admin->id,
        'email' => 'declines@example.com',
        'role' => TeamRole::Member->value,
    ]);

    $invitee = User::factory()->withTeam()->create(['email' => 'declines@example.com']);
    actingAs($invitee);

    visit('/app/'.$invitee->teams()->first()->uuid)
        ->assertSee($tenant->name)
        ->click('Decline')
        ->click('Confirm')
        ->assertSee('Invitation declined');

    assertDatabaseMissing('invitations', ['id' => $invitation->id]);
});
