<?php

use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertTrue;

it('shows the transfer ownership section to administrators', function () {
    $admin = User::factory()->create();
    $tenant = $admin->currentTeam;
    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->assertSee('Transfer ownership')
        ->assertPresent('[data-testid="transfer-ownership-button"]');
});

it('disables the transfer ownership button when there are no other members', function () {
    $admin = User::factory()->create();
    $tenant = $admin->currentTeam;
    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->assertPresent('[data-testid="transfer-ownership-button"][disabled]');
});

it('transfers ownership to another team member', function () {
    $admin = User::factory()->create();
    $tenant = $admin->currentTeam;
    $member = User::factory()->create(['name' => 'Future Admin']);
    $tenant->members()->attach($member, ['role' => TeamRole::Member]);

    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="transfer-ownership-button"]')
        ->click('[data-testid="transfer-ownership-select"]')
        ->click('.fi-dropdown-panel:visible [role="option"]:has-text("Future Admin")')
        ->click('[data-testid="transfer-ownership-confirm"]')
        ->assertSee('Ownership transferred');

    assertDatabaseHas('teams', ['id' => $tenant->id, 'user_id' => $member->id]);
    assertTrue($tenant->fresh()->isAdministeredBy($member));
});
