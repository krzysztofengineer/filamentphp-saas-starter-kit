<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertTrue;

it('updates the user name', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'me@example.com',
    ]);
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/settings')
        ->fill('[data-testid="account-profile-name"]', 'Updated Name')
        ->click('[data-testid="account-profile-save"]')
        ->assertAttributeContains('.fi-user-avatar', 'alt', 'Updated Name');

    assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});

it('changes the password', function () {
    $user = User::factory()->create([
        'email' => 'me@example.com',
        'password' => Hash::make('old-password'),
    ]);
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/settings')
        ->fill('[data-testid="account-password-current"]', 'old-password')
        ->fill('[data-testid="account-password-new"]', 'new-password-123')
        ->fill('[data-testid="account-password-confirm"]', 'new-password-123')
        ->click('[data-testid="account-password-save"]')
        ->assertSee('Password changed');

    assertTrue(Hash::check('new-password-123', $user->fresh()->password));
});

it('rejects the password change when the current password is wrong', function () {
    $user = User::factory()->create([
        'email' => 'me@example.com',
        'password' => Hash::make('old-password'),
    ]);
    actingAs($user);

    visit('/app/'.$user->currentTeam->uuid.'/account/settings')
        ->fill('[data-testid="account-password-current"]', 'totally-wrong')
        ->fill('[data-testid="account-password-new"]', 'new-password-123')
        ->fill('[data-testid="account-password-confirm"]', 'new-password-123')
        ->click('[data-testid="account-password-save"]')
        ->assertSee('current password is incorrect');

    assertTrue(Hash::check('old-password', $user->fresh()->password));
});
