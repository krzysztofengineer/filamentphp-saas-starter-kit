<?php

namespace App\Models;

use App\TeamRole;
use Database\Factories\TeamFactory;
use Filament\Models\Contracts\HasAvatar;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class Team extends Model implements HasAvatar
{
    /** @use HasFactory<TeamFactory> */
    use Billable, HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $team): void {
            if (empty($team->uuid)) {
                $team->uuid = (string) Str::uuid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function administrators()
    {
        return $this->users()->wherePivot('role', TeamRole::Administrator->value);
    }

    public function managers()
    {
        return $this->users()->wherePivot('role', TeamRole::Manager->value);
    }

    public function members()
    {
        return $this->users()->wherePivot('role', TeamRole::Member->value);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function roleFor(User $user): ?TeamRole
    {
        $pivot = $this->users()->where('users.id', $user->id)->first()?->pivot;

        return $pivot?->role ? TeamRole::from($pivot->role) : null;
    }

    public function isAdministeredBy(User $user): bool
    {
        return $this->roleFor($user) === TeamRole::Administrator;
    }

    public function canBeManagedBy(User $user): bool
    {
        return $this->roleFor($user)?->canManageTeam() === true;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->roleFor($user)?->canDeleteTeam() === true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $bg = Color::convertToHex(Color::Red[600]);
        $fg = '#ffffff';
        $initial = trim((string) $this->name) === '' ? 'T' : strtoupper(mb_substr($this->name, 0, 1));

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="{$bg}"/><text x="32" y="32" font-family="-apple-system, system-ui, sans-serif" font-size="28" font-weight="700" fill="{$fg}" text-anchor="middle" dominant-baseline="central">{$initial}</text></svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
