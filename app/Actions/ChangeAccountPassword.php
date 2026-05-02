<?php

namespace App\Actions;

use App\Models\User;

class ChangeAccountPassword
{
    public function handle(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }
}
