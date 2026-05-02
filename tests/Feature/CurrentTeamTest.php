<?php

use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;

it('sets current_team_id when a user is created', function () {
    $user = User::factory()->create();

    assertEquals($user->ownedTeams()->first()->id, $user->fresh()->current_team_id);
});

it('updates current_team_id when the user creates a new team', function () {
    $user = User::factory()->create();
    actingAs($user);

    $newTeam = Team::create(['name' => 'Side Project', 'user_id' => $user->id]);
    $newTeam->users()->attach($user, ['role' => TeamRole::Administrator->value]);
    $user->update(['current_team_id' => $newTeam->id]);

    assertEquals($newTeam->id, $user->fresh()->current_team_id);
});

it('updates current_team_id when switching tenant', function () {
    $user = User::factory()->create();
    $secondTeam = Team::factory()->create(['user_id' => $user->id]);
    $secondTeam->members()->attach($user, ['role' => TeamRole::Administrator]);

    actingAs($user);

    $this->get('/app/'.$secondTeam->uuid)->assertSuccessful();

    assertEquals($secondTeam->id, $user->fresh()->current_team_id);
});

it('returns currentTeam as the default tenant', function () {
    $user = User::factory()->create();

    $defaultTenant = $user->getDefaultTenant(Filament::getPanel('app'));

    assertEquals($user->currentTeam->id, $defaultTenant->id);
});
