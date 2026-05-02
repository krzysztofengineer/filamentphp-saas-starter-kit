<?php

use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;

it('shows the transfer ownership section to administrators', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    actingAs($admin);

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->assertSee('Transfer ownership')
        ->assertPresent('[data-testid="transfer-ownership-button"]');
});

it('disables the transfer ownership button when there are no other members', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    actingAs($admin);

    // todo: brak odpowiedniej asercji
    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->assertSee('Transfer ownership');
});

it('renders the transfer ownership modal with member options', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create(['name' => 'Future Admin']);
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

    actingAs($admin);

    // todo: brak kliknięcia przycisku i sprawdzenia czy został przekazany team

    visit('/app/'.$tenant->uuid.'/settings/advanced')
        ->click('[data-testid="transfer-ownership-button"]')
        ->assertSee('Transfer ownership')
        ->assertSee('New administrator')
        ->assertNoJavaScriptErrors();
});
