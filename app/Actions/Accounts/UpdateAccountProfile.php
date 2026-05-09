<?php

namespace App\Actions\Accounts;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateAccountProfile
{
    public function handle(User $user, array $attributes): void
    {
        if (array_key_exists('avatar', $attributes) && filled($user->avatar) && $user->avatar !== $attributes['avatar']) {
            Storage::disk('user-avatars')->delete($user->avatar);
        }

        $user->update($attributes);
    }
}
