<?php

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('updates the user name from the account profile form', function () {
    $user = User::factory()->withTeam()->create([
        'name' => 'Original Name',
        'email' => 'me@example.com',
    ]);
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/settings')
        ->fill('[data-testid="account-profile-name-input"]', 'Updated Name')
        ->click('[data-testid="account-profile-save"]')
        ->assertSee('Account saved')
        ->assertNoJavaScriptErrors();

    assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});

it('changes the password when the current password is correct', function () {
    $user = User::factory()->withTeam()->create([
        'email' => 'me@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/settings')
        ->fill('[data-testid="account-password-current"]', 'old-password')
        ->fill('[data-testid="account-password-new"]', 'new-password-123')
        ->fill('[data-testid="account-password-confirm"]', 'new-password-123')
        ->click('[data-testid="account-password-save"]')
        ->assertSee('Password changed')
        ->assertNoJavaScriptErrors();

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('rejects the password change when the current password is wrong', function () {
    $user = User::factory()->withTeam()->create([
        'email' => 'me@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/settings')
        ->fill('[data-testid="account-password-current"]', 'totally-wrong')
        ->fill('[data-testid="account-password-new"]', 'new-password-123')
        ->fill('[data-testid="account-password-confirm"]', 'new-password-123')
        ->click('[data-testid="account-password-save"]')
        ->assertSee('current password is incorrect');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('schedules account deletion when the user administers no teams', function () {
    $user = User::factory()->create([
        'email' => 'free-agent@example.com',
    ]);
    $team = Team::factory()->create();
    $team->users()->attach($user, ['role' => TeamRole::Member->value]);
    $user->update(['current_team_id' => $team->id]);

    actingAs($user);

    visit('/app/'.$team->uuid.'/account/advanced')
        ->click('[data-testid="delete-account-button"]')
        ->assertSee('permanently deleted')
        ->click('[data-testid="delete-account-confirm"]')
        ->assertPathIs('/app/login');

    expect($user->fresh()->scheduled_for_deletion_at)->not->toBeNull();
});

it('blocks account deletion when the user still administers a team', function () {
    $user = User::factory()->withTeam()->create([
        'email' => 'still-admin@example.com',
    ]);
    $tenant = $user->teams()->first();
    actingAs($user);

    visit('/app/'.$tenant->uuid.'/account/advanced')
        ->click('[data-testid="delete-account-button"]')
        ->assertSee('Leave or delete the teams you administer first')
        ->assertNoJavaScriptErrors();

    expect($user->fresh()->scheduled_for_deletion_at)->toBeNull();
});
