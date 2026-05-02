<?php

namespace App\Models;

use App\Observers\TeamInvitationObserver;
use App\TeamRole;
use Database\Factories\TeamInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[ObservedBy(TeamInvitationObserver::class)]
class TeamInvitation extends Model
{
    /** @use HasFactory<TeamInvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'invited_by_user_id',
        'email',
        'role',
        'token',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'role' => TeamRole::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $invitation): void {
            if (empty($invitation->token)) {
                $invitation->token = self::generateToken();
            }

            $invitation->email = strtolower($invitation->email);
        });
    }

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

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }
}
