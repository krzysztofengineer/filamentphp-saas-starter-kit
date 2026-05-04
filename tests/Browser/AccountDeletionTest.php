<?php

use App\Models\User;
use App\TeamRole;
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
        ->assertSee('permanently deleted')
        ->click('[data-testid="delete-account-confirm"]')
        ->assertPathIs('/app/login');

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
        ->assertSee('Leave it or transfer ownership before deleting your account')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('cancels a scheduled account deletion via the dashboard banner', function () {
    Config::set('account.deletion_grace_days', 14);

    $user = User::factory()->create([
        'email' => 'reconsidered@example.com',
        'deleted_at' => now()->subDays(3),
    ]);
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid)
        ->assertSee('Your account will be deleted in 11 days')
        ->click('[data-testid="cancel-account-deletion"]')
        ->assertSee('Account deletion cancelled')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
});
