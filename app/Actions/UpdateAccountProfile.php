<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateAccountProfile
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        if (array_key_exists('avatar', $attributes)) {
            $new = $attributes['avatar'];

            if (filled($user->avatar) && $user->avatar !== $new) {
                Storage::disk('user-avatars')->delete($user->avatar);
            }
        }

        $user->update($attributes);
    }
}
