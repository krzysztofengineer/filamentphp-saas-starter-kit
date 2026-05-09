<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMembership extends Pivot
{
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
    ];

    protected $casts = [
        'role' => TeamRole::class,
    ];
}
