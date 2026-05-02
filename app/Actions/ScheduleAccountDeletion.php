<?php

namespace App\Actions;

use App\Models\User;

class ScheduleAccountDeletion
{
    public function handle(User $user): void
    {
        $user->update(['scheduled_for_deletion_at' => now()]);
    }
}
