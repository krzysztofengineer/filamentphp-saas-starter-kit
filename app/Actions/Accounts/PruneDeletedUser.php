<?php

namespace App\Actions\Accounts;

use App\Models\User;

class PruneDeletedUser
{
    public function handle(User $user): void
    {
        $user->teams()->detach();
        $user->delete();
    }
}
