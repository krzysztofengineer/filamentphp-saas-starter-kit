<?php

use App\BillingInterval;
use App\Models\Invitation;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('lets a paid owner send an invitation by email', function () {
    $owner = User::factory()->starterPlan(BillingInterval::Monthly)->withTeam()->create();
    $tenant = $owner->teams()->first();
    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->click('[data-testid="invite-button"]')
        ->fill('[data-testid="invite-email"]', 'invitee@example.com')
        ->click('[data-testid="invite-submit-button"]')
        ->assertSee('Invitation sent')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('invitations', [
        'team_id' => $tenant->id,
        'email' => 'invitee@example.com',
        'accepted_at' => null,
    ]);
});

it('disables invitations for users on the free plan', function () {
    $owner = User::factory()->withTeam()->create();
    $tenant = $owner->teams()->first();
    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('requires the Starter or Pro plan')
        ->assertNoJavaScriptErrors();
});

it('lets the invitee accept an invitation from the dashboard', function () {
    $owner = User::factory()->starterPlan(BillingInterval::Monthly)->withTeam()->create();
    $tenant = $owner->teams()->first();

    Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
        'email' => 'invitee@example.com',
    ]);

    $invitee = User::factory()->starterPlan(BillingInterval::Monthly)->withTeam()->create([
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
        'role' => TeamRole::Member->value,
    ]);
});

it('lets the owner revoke a pending invitation', function () {
    $owner = User::factory()->starterPlan(BillingInterval::Monthly)->withTeam()->create();
    $tenant = $owner->teams()->first();

    $invitation = Invitation::create([
        'team_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
        'email' => 'invitee@example.com',
    ]);

    actingAs($owner);

    visit('/app/'.$tenant->uuid.'/settings/members')
        ->assertSee('invitee@example.com')
        ->click('[data-testid="revoke-invitation-button"]')
        ->click('[data-testid="revoke-invitation-confirm"]')
        ->assertSee('Invitation revoked')
        ->assertNoJavaScriptErrors();

    assertDatabaseMissing('invitations', ['id' => $invitation->id]);
});
