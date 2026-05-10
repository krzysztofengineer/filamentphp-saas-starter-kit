<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertTrue;

it('shows the transfer ownership section to administrators', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $admin->id]);
    $admin->update(['current_team_id' => $team->id]);

    actingAs($admin);

    visit('/app/'.$team->uuid.'/settings/profile')
        ->assertSee('Transfer ownership')
        ->assertPresent('[data-testid="transfer-ownership"]');
});

it('disables the transfer ownership button when there are no other members', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $admin->id]);
    $admin->update(['current_team_id' => $team->id]);

    actingAs($admin);

    visit('/app/'.$team->uuid.'/settings/profile')
        ->assertPresent('[data-testid="transfer-ownership"][aria-disabled="true"]');
});

it('transfers ownership to another team member', function () {
    $admin = User::factory()->create();
    $tenant = Team::factory()->create(['user_id' => $admin->id]);
    $admin->update(['current_team_id' => $tenant->id]);

    $member = User::factory()->create(['name' => 'Future Admin']);
    $tenant->members()->attach($member, ['role' => TeamRole::Member]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/profile')
        ->click('[data-testid="transfer-ownership"]')
        ->click('[data-testid="transfer-ownership-select"]')
        ->click('.fi-dropdown-panel:visible [role="option"]:has-text("Future Admin")')
        ->click('[data-testid="transfer-ownership-confirm"]')
        ->assertSee('Ownership transferred');

    assertDatabaseHas('teams', ['id' => $tenant->id, 'user_id' => $member->id]);
    assertTrue($member->can('update', $tenant->fresh()));
    assertDatabaseHas('team_user', [
        'team_id' => $tenant->id,
        'user_id' => $member->id,
        'role' => TeamRole::Administrator->value,
    ]);
    assertDatabaseHas('team_user', [
        'team_id' => $tenant->id,
        'user_id' => $admin->id,
        'role' => TeamRole::Member->value,
    ]);
});

it('disables the transfer ownership and delete actions on a personal team', function () {
    $owner = User::factory()->create();

    actingAs($owner);

    visit('/app/'.$owner->currentTeam->uuid.'/settings/profile')
        ->assertPresent('[data-testid="transfer-ownership"][aria-disabled="true"]')
        ->assertPresent('[data-testid="delete-team"][aria-disabled="true"]');
});
