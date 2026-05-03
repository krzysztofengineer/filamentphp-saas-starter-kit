<?php

use App\Actions\DeleteTeam;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('schedules account deletion once the user no longer administers any team', function () {
    $user = User::factory()->create([
        'email' => 'free-agent@example.com',
    ]);
    $hostTeam = Team::factory()->create();
    $hostTeam->members()->attach($user, ['role' => TeamRole::Member]);

    (new DeleteTeam)->handle($user->currentTeam, $user);
    $user->refresh();

    actingAs($user);

    visit('/app/'.$hostTeam->uuid.'/account/advanced')
        ->click('[data-testid="delete-account"]')
        ->assertSee('permanently deleted')
        ->click('[data-testid="delete-account-confirm"]')
        ->assertPathIs('/app/login');

    assertDatabaseMissing('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('blocks account deletion when the user still administers a team', function () {
    $user = User::factory()->create([
        'email' => 'still-admin@example.com',
    ]);
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/advanced')
        ->click('[data-testid="delete-account"]')
        ->assertSee('Leave or delete the teams you administer first')
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
