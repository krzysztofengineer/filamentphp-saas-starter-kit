<?php

namespace App\Actions;

use App\Models\User;

class CancelAccountDeletion
{
    public function handle(User $user): void
    {
        $user->update(['deleted_at' => null]);
    }
}
