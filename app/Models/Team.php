<?php

namespace App\Models;

use App\Filament\AvatarProviders\TeamLogoProvider;
use App\Observers\TeamObserver;
use App\TeamRole;
use Database\Factories\TeamFactory;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Cashier\Billable;

#[ObservedBy(TeamObserver::class)]
class Team extends Model implements HasAvatar
{
    /** @use HasFactory<TeamFactory> */
    use Billable, HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'logo',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')
            ->withPivot('role')
            ->using(TeamMembership::class);
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

    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
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
        return app(TeamLogoProvider::class)->get($this);
    }
}
