<?php

namespace App\Models;

use App\BillingInterval;
use App\BillingPlan;
use App\Notifications\ResetPassword;
use App\TeamRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['name', 'email', 'password', 'current_team_id', 'scheduled_for_deletion_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasDefaultTenant, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, HasPushSubscriptions, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'scheduled_for_deletion_at' => 'datetime',
        ];
    }

    public function isScheduledForDeletion(): bool
    {
        return $this->scheduled_for_deletion_at !== null;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function currentPlan(): ?BillingPlan
    {
        if (! $this->subscribed('default')) {
            return null;
        }

        foreach (BillingPlan::cases() as $plan) {
            foreach (BillingInterval::cases() as $interval) {
                $priceId = $plan->priceId($interval);

                if ($priceId && $this->subscribedToPrice($priceId, 'default')) {
                    return $plan;
                }
            }
        }

        return null;
    }

    public function maxTeamMemberships(): ?int
    {
        $plan = $this->currentPlan();

        return $plan === null ? BillingPlan::freeMaxTeamMemberships() : $plan->maxTeamMemberships();
    }

    public function canAddMoreTeams(): bool
    {
        $limit = $this->maxTeamMemberships();

        return $limit === null || $this->teams()->count() < $limit;
    }

    public function canInviteMembers(): bool
    {
        return $this->currentPlan() !== null;
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('role');
    }

    public function ownedTeams()
    {
        return $this->teams()->wherePivot('role', TeamRole::Owner->value);
    }

    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'app' => true,
            default => false,
        };
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()
            ->where('teams.id', $tenant->id)
            ->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->teams;
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->currentTeam;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $bg = Color::convertToHex(Color::Gray[200]);
        $fg = Color::convertToHex(Color::Gray[700]);
        $initial = trim((string) $this->name) === '' ? '?' : strtoupper(mb_substr($this->name, 0, 1));

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="{$bg}"/><text x="32" y="32" font-family="-apple-system, system-ui, sans-serif" font-size="28" font-weight="600" fill="{$fg}" text-anchor="middle" dominant-baseline="central">{$initial}</text></svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
