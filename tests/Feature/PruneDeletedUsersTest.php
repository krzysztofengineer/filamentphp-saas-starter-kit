<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('hard-deletes users scheduled for deletion past the threshold', function () {
    $oldUser = User::factory()->withTeam()->create([
        'scheduled_for_deletion_at' => now()->subDays(31),
    ]);
    $recentUser = User::factory()->withTeam()->create([
        'scheduled_for_deletion_at' => now()->subDays(2),
    ]);
    $aliveUser = User::factory()->withTeam()->create();

    Artisan::call('users:prune');

    assertDatabaseMissing('users', ['id' => $oldUser->id]);
    assertDatabaseHas('users', ['id' => $recentUser->id]);
    assertDatabaseHas('users', ['id' => $aliveUser->id]);
});
