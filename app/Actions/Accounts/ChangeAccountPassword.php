<?php

namespace App\Actions\Accounts;

use App\Models\User;

class ChangeAccountPassword
{
    public function handle(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }
}
