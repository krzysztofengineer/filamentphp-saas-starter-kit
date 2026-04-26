<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\AccountSettings\Pages\AccountBilling;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class CreateTeam extends RegisterTenant
{
    protected static ?string $slug = 'new';

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user !== null && ! $user->canAddMoreTeams() && ! $this->hasPendingInvitations()) {
            Notification::make()
                ->danger()
                ->title('Free plan · team limit reached')
                ->body('The Free plan allows a single team. Upgrade to Starter or Pro to add more.')
                ->send();

            $this->redirect(AccountBilling::getUrl(tenant: $user->teams()->first()));

            return;
        }

        parent::mount();
    }

    public function getMaxWidth(): Width|string|null
    {
        return Width::Medium;
    }

    public static function getLabel(): string
    {
        return 'Create Team';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Team name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->extraInputAttributes(['data-testid' => 'create-team-name']),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);

        $team->users()->attach(Auth::user(), ['role' => TeamRole::Owner->value]);

        return $team;
    }

    public function hasPendingInvitations(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return Invitation::query()
            ->whereNull('accepted_at')
            ->where('email', strtolower($user->email))
            ->exists();
    }
}
