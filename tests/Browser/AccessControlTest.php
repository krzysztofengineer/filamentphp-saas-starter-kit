<?php

use App\Models\User;
use App\TeamRole;

use function Pest\Laravel\actingAs;

it('grants administrators full access to the team settings cluster', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    actingAs($admin);

    foreach (['profile', 'members', 'subscription', 'advanced'] as $page) {
        $response = $this->get('/app/'.$tenant->uuid.'/settings/'.$page);
        expect($response->status())->toBe(200, "admin should access /settings/{$page}");
    }
});

it('grants managers access to team settings except the advanced page', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $manager = User::factory()->create();
    $tenant->users()->attach($manager, ['role' => TeamRole::Manager->value]);
    $manager->update(['current_team_id' => $tenant->id]);
    actingAs($manager);

    foreach (['profile', 'members', 'subscription'] as $page) {
        $response = $this->get('/app/'.$tenant->uuid.'/settings/'.$page);
        expect($response->status())->toBe(200, "manager should access /settings/{$page}");
    }

    $advanced = $this->get('/app/'.$tenant->uuid.'/settings/advanced');
    expect($advanced->status())->toBe(403);
});

it('blocks regular members from every team settings page', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create();
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);
    $member->update(['current_team_id' => $tenant->id]);
    actingAs($member);

    foreach (['profile', 'members', 'subscription', 'advanced'] as $page) {
        $response = $this->get('/app/'.$tenant->uuid.'/settings/'.$page);
        expect($response->status())->toBe(403, "member should be blocked from /settings/{$page}");
    }
});

it('lets every role view the dashboard and account pages', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create();
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);
    $member->update(['current_team_id' => $tenant->id]);
    actingAs($member);

    foreach (['', '/account/settings', '/account/advanced'] as $path) {
        $response = $this->get('/app/'.$tenant->uuid.$path);
        expect($response->status())->toBe(200, "member should access {$path}");
    }
});

it('blocks billing checkout for non-managers', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create();
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);
    $member->update(['current_team_id' => $tenant->id]);
    actingAs($member);

    $response = $this->get('/billing/'.$tenant->uuid.'/portal');
    expect($response->status())->toBe(403);
});

it('blocks access to teams the user does not belong to', function () {
    $insider = User::factory()->withTeam()->create();
    $outsider = User::factory()->create();
    $tenant = $insider->teams()->first();
    actingAs($outsider);

    $response = $this->get('/app/'.$tenant->uuid);
    expect($response->status())->toBeIn([302, 403, 404]);
});

it('removes the team settings tenant menu items for plain members', function () {
    $admin = User::factory()->withTeam()->create();
    $tenant = $admin->teams()->first();
    $member = User::factory()->create();
    $tenant->users()->attach($member, ['role' => TeamRole::Member->value]);
    $member->update(['current_team_id' => $tenant->id]);
    actingAs($member);

    $response = $this->get('/app/'.$tenant->uuid);
    expect($response->status())->toBe(200);

    $body = $response->getContent();
    expect($body)->not->toContain('Team details')
        ->and($body)->not->toContain('href="/app/'.$tenant->uuid.'/settings/advanced"');
});

it('lets a member create a new team since invitations are not gated', function () {
    $member = User::factory()->withTeam()->create();
    $tenant = $member->teams()->first();
    actingAs($member);

    $response = $this->get('/app/new');
    expect($response->status())->toBe(200);
    expect($response->getContent())->toContain('Create Team');
});
