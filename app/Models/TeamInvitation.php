<?php

namespace App\Models;

use App\Enums\TeamRole;
use App\Observers\TeamInvitationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[ObservedBy(TeamInvitationObserver::class)]
class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'email',
        'role',
        'token',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'role' => TeamRole::class,
    ];

    public static function generateToken(): string
    {
        do {
            $token = Str::random(40);
        } while (self::query()->where('token', $token)->exists());

        return $token;
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
