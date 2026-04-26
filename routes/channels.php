<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id): bool {
    return $user->id === $id;
});

Broadcast::channel('team.{teamId}', function (User $user, int $teamId): bool {
    return Team::query()
        ->whereKey($teamId)
        ->whereHas('users', fn ($q) => $q->whereKey($user->id))
        ->exists();
});
