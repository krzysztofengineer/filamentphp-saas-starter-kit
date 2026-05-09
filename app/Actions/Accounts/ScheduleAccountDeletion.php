<?php

namespace App\Actions\Accounts;

use App\Models\User;

class ScheduleAccountDeletion
{
    public function handle(User $user): void
    {
        $user->update(['deleted_at' => now()]);
    }
}
