<?php

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertTrue;

it('updates the user name', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'me@example.com',
    ]);
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/settings')
        ->fill('[data-testid="account-profile-name-input"]', 'Updated Name')
        ->click('[data-testid="account-profile-save"]')
        ->wait(1)
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});

it('changes the password', function () {
    $user = User::factory()->create([
        'email' => 'me@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/settings')
        ->fill('[data-testid="account-password-current"]', 'old-password')
        ->fill('[data-testid="account-password-new"]', 'new-password-123')
        ->fill('[data-testid="account-password-confirm"]', 'new-password-123')
        ->click('[data-testid="account-password-save"]')
        ->assertSee('Password changed')
        ->assertNoJavaScriptErrors();

    assertTrue(Hash::check('new-password-123', $user->fresh()->password));
});

it('rejects the password change when the current password is wrong', function () {
    $user = User::factory()->create([
        'email' => 'me@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/settings')
        ->fill('[data-testid="account-password-current"]', 'totally-wrong')
        ->fill('[data-testid="account-password-new"]', 'new-password-123')
        ->fill('[data-testid="account-password-confirm"]', 'new-password-123')
        ->click('[data-testid="account-password-save"]')
        ->assertSee('current password is incorrect');

    assertTrue(Hash::check('old-password', $user->fresh()->password));
});

it('schedules account deletion when the user administers no teams', function () {
    $user = User::factory()->create([
        'email' => 'free-agent@example.com',
    ]);
    $user->ownedTeams()->delete();

    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Member]);
    $user->update(['current_team_id' => $team->id]);

    actingAs($user);

    visit('/app/'.$team->uuid.'/account/advanced')
        ->click('[data-testid="delete-account-button"]')
        ->assertSee('permanently deleted')
        ->click('[data-testid="delete-account-confirm"]')
        ->assertPathIs('/app/login');

    assertDatabaseMissing('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('blocks account deletion when the user still administers a team', function () {
    $user = User::factory()->create([
        'email' => 'still-admin@example.com',
    ]);
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/advanced')
        ->click('[data-testid="delete-account-button"]')
        ->assertSee('Leave or delete the teams you administer first')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
});
