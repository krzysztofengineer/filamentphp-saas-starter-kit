<?php

use App\Console\Commands\PruneDeletedUsers;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('hard-deletes users past the configured grace period', function () {
    Config::set('account.deletion_grace_days', 30);

    $oldUser = User::factory()->create([
        'deleted_at' => now()->subDays(31),
    ]);
    $recentUser = User::factory()->create([
        'deleted_at' => now()->subDays(2),
    ]);
    $aliveUser = User::factory()->create();

    Artisan::call(PruneDeletedUsers::class);

    assertDatabaseMissing('users', ['id' => $oldUser->id]);
    assertDatabaseHas('users', ['id' => $recentUser->id]);
    assertDatabaseHas('users', ['id' => $aliveUser->id]);
});

it('uses the configured grace period when no --days option is given', function () {
    Config::set('account.deletion_grace_days', 5);

    $user = User::factory()->create([
        'deleted_at' => now()->subDays(6),
    ]);

    Artisan::call(PruneDeletedUsers::class);

    assertDatabaseMissing('users', ['id' => $user->id]);
});

it('detaches the pruned user from all teams they belonged to', function () {
    Config::set('account.deletion_grace_days', 30);

    $user = User::factory()->create([
        'deleted_at' => now()->subDays(31),
    ]);
    $otherTeam = Team::factory()->create();
    $otherTeam->members()->attach($user, ['role' => TeamRole::Member]);

    Artisan::call(PruneDeletedUsers::class);

    assertDatabaseMissing('team_user', ['user_id' => $user->id]);
});

it('honours a --days override that is shorter than the configured grace period', function () {
    Config::set('account.deletion_grace_days', 30);

    $user = User::factory()->create([
        'deleted_at' => now()->subDays(8),
    ]);

    Artisan::call(PruneDeletedUsers::class, ['--days' => 7]);

    assertDatabaseMissing('users', ['id' => $user->id]);
});
