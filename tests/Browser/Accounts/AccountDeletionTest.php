<?php

use App\Enums\TeamRole;
use App\Models\User;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('schedules account deletion for a user whose only team is their solo personal team', function () {
    $user = User::factory()->create([
        'email' => 'solo@example.com',
    ]);

    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/advanced')
        ->click('[data-testid="delete-account"]')
        ->assertSee('Permanently removed')
        ->click('[data-testid="delete-account-confirm"]')
        ->assertPathIs('/app/pending-deletion');

    assertDatabaseMissing('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('blocks account deletion when the user administers a team that has other members', function () {
    $user = User::factory()->create([
        'email' => 'team-admin@example.com',
    ]);
    $teammate = User::factory()->create();
    $user->currentTeam->members()->attach($teammate, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/advanced')
        ->click('[data-testid="delete-account"]')
        ->assertSee('Leave it or transfer ownership first');

    assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('redirects a scheduled-for-deletion user to the pending deletion page on every request', function () {
    Config::set('account.deletion_grace_days', 14);

    $user = User::factory()->create([
        'email' => 'reconsidered@example.com',
        'deleted_at' => now()->subDays(3),
    ]);
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/settings')
        ->assertPathIs('/app/pending-deletion')
        ->assertSee('Your account will be deleted in 11 days');
});

it('cancels a scheduled account deletion from the pending deletion page', function () {
    $user = User::factory()->create([
        'email' => 'reconsidered@example.com',
        'deleted_at' => now()->subDays(3),
    ]);
    actingAs($user);

    visit('/app/pending-deletion')
        ->click('[data-testid="cancel-account-deletion"]')
        ->assertSee('Cancel account deletion?')
        ->click('[data-testid="cancel-account-deletion-confirm"]')
        ->assertSee('Account deletion cancelled');

    assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('lets a scheduled-for-deletion user sign out from the pending deletion page', function () {
    $user = User::factory()->create([
        'email' => 'goodbye@example.com',
        'deleted_at' => now()->subDays(1),
    ]);
    actingAs($user);

    visit('/app/pending-deletion')
        ->click('[data-testid="pending-deletion-sign-out"]')
        ->assertPathIs('/app/login');

    assertDatabaseHas('users', ['id' => $user->id, 'email' => 'goodbye@example.com']);
});
