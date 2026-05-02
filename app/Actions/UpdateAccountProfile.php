<?php

namespace App\Actions;

use App\Models\User;

class UpdateAccountProfile
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        $user->update($attributes);
    }
}
