<?php

use App\Models\Team;
use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

it('does not allow regular members to access team members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);

    $team->members()->attach($user, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->assertNotPresent('@team-members');
});

it('lists all team members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);
    $otherUser = User::factory()->create();

    $team->members()->attach($user, ['role' => TeamRole::Administrator]);
    $team->members()->attach($otherUser, ['role' => TeamRole::Member]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->assertSee($user->name)
        ->assertSee($otherUser->name);
});

it('can invite new members', function () {
    $team = Team::factory()->create(['name' => 'Test']);
    $user = User::factory()->create(['current_team_id' => $team->id]);

    $team->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    visit('/app')
        ->click('.fi-topbar button.fi-tenant-menu-trigger')
        ->click('@team-members')
        ->click('@invite-button')
        ->fill('@invite-email', 'test@example.com')
        ->debug();
});

// it('lists members with their names and the Owner badge for the actor', function () {
//     $admin = User::factory()->withTeam()->create(['name' => 'Admin Person']);
//     $tenant = $admin->teams()->first();

//     $manager = User::factory()->create(['name' => 'Manager Person']);
//     $tenant->users()->attach($manager, ['role' => TeamRole::Manager->value]);

//     $member = User::factory()->create(['name' => 'Member Person']);
//     $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

//     actingAs($admin);

//     visit('/app/'.$tenant->uuid.'/settings/members')
//         ->assertSee('Admin Person')
//         ->assertSee('Manager Person')
//         ->assertSee('Member Person')
//         ->assertSee('Owner')
//         ->assertNoJavaScriptErrors();
// });

// it('renders an inline role select on each member row', function () {
//     $admin = User::factory()->withTeam()->create();
//     $tenant = $admin->teams()->first();
//     $member = User::factory()->create(['name' => 'Promotable']);
//     $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

//     actingAs($admin);

//     visit('/app/'.$tenant->uuid.'/settings/members')
//         ->assertSee('Promotable')
//         ->assertPresent('[data-testid="member-role-select"]')
//         ->assertNoJavaScriptErrors();
// });

// it('shows the Owner badge for the actor regardless of role', function () {
//     $admin = User::factory()->withTeam()->create();
//     $tenant = $admin->teams()->first();
//     $member = User::factory()->create();
//     $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

//     actingAs($admin);

//     expect($tenant->administrators()->count())->toBe(1);

//     visit('/app/'.$tenant->uuid.'/settings/members')
//         ->assertSee('Owner');
// });

// it('removes a member from the team', function () {
//     $admin = User::factory()->withTeam()->create();
//     $tenant = $admin->teams()->first();
//     $member = User::factory()->create(['name' => 'Removable Member']);
//     $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);

//     actingAs($admin);

//     visit('/app/'.$tenant->uuid.'/settings/members')
//         ->assertSee('Removable Member')
//         ->click('button[aria-label="Actions"]')
//         ->click('[data-testid="remove-member-button"]')
//         ->click('[data-testid="remove-member-confirm"]')
//         ->assertSee('Member removed')
//         ->assertNoJavaScriptErrors();

//     assertDatabaseMissing('team_user', ['team_id' => $tenant->id, 'user_id' => $member->id]);
// });

// it('does not show the remove or change-role action on the actor row', function () {
//     $admin = User::factory()->withTeam()->create();
//     $tenant = $admin->teams()->first();
//     actingAs($admin);

//     visit('/app/'.$tenant->uuid.'/settings/members')
//         ->assertSee($admin->name);
// });

// it('shows the empty invitations state and an invite button there', function () {
//     $admin = User::factory()->withTeam()->create();
//     $tenant = $admin->teams()->first();
//     actingAs($admin);

//     visit('/app/'.$tenant->uuid.'/settings/members')
//         ->assertSee('No pending invitations')
//         ->assertPresent('[data-testid="invite-button-empty"]');
// });
