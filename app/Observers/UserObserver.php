<?php

namespace App\Observers;

use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    public function created(User $user): void
    {
        DB::transaction(function () use ($user) {
            $team = $user->teams()->create([
                'name' => "{$user->name}'s Team",
            ]);

            $team->users()->attach($user, ['role' => TeamRole::Administrator]);
        });
    }
}
