<?php

namespace App\Filament\Widgets;

use App\Filament\Clusters\AccountSettings\Pages\AccountBilling;
use App\Filament\Support\CategoryHeading;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class TeamInvitationsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $canInvite = $this->canInvite();

        return $table
            ->heading(CategoryHeading::make('heroicon-o-envelope', 'primary', 'Invitations'))
            ->description('Pending invitations — recipients will see them after they sign in.')
            ->records(fn (): Collection => $this->getInvitations())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('email')
                            ->weight('bold'),
                        TextColumn::make('created_at')
                            ->state(fn (Invitation $record): string => 'Sent '.$record->created_at->diffForHumans())
                            ->color('gray')
                            ->size(TextSize::Small),
                    ]),
                ]),
            ])
            ->headerActions([
                $this->inviteAction()->extraAttributes(['data-testid' => 'invite-button']),
            ])
            ->recordActions([
                $this->revokeInvitationAction(),
            ])
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-envelope')
            ->emptyStateHeading('No pending invitations')
            ->emptyStateDescription($canInvite
                ? 'Invite someone by email to give them access to this team.'
                : 'Inviting members requires the Starter or Pro plan.'
            )
            ->emptyStateActions($canInvite
                ? [
                    $this->inviteAction()
                        ->name('inviteFromEmptyState')
                        ->extraAttributes(['data-testid' => 'invite-button-empty'])
                        ->button()
                        ->outlined(),
                ]
                : [
                    Action::make('upgradeFromEmptyState')
                        ->label('Upgrade plan')
                        ->icon(Heroicon::OutlinedArrowUpCircle)
                        ->button()
                        ->url(fn (): ?string => $this->subscriptionUrl()),
                ]
            );
    }

    private function getInvitations(): Collection
    {
        /** @var Team|null $team */
        $team = Filament::getTenant();

        if ($team === null) {
            return collect();
        }

        return $team->invitations()->whereNull('accepted_at')->orderBy('created_at')->get();
    }

    private function canInvite(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user !== null && $user->canInviteMembers();
    }

    private function subscriptionUrl(): ?string
    {
        /** @var Team|null $team */
        $team = Filament::getTenant();

        return $team === null ? null : AccountBilling::getUrl(tenant: $team);
    }

    private function inviteAction(): Action
    {
        return Action::make('invite')
            ->label('Invite member')
            ->icon(Heroicon::OutlinedUserPlus)
            ->disabled(fn (): bool => ! $this->canInvite())
            ->tooltip(fn (): ?string => $this->canInvite() ? null : 'Available on the Starter or Pro plan')
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedUserPlus)
            ->modalHeading('Invite a new team member')
            ->modalDescription('Enter their email — they will see the invitation after signing in.')
            ->modalSubmitActionLabel('Send invitation')
            ->modalSubmitAction(fn (Action $action) => $action->extraAttributes(['data-testid' => 'invite-submit-button']))
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('teammate@example.com')
                    ->prefixIcon(Heroicon::OutlinedEnvelope)
                    ->extraInputAttributes(['data-testid' => 'invite-email']),
            ])
            ->action(function (array $data): void {
                /** @var Team|null $team */
                $team = Filament::getTenant();

                if ($team === null) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                if (! $user->canInviteMembers()) {
                    Notification::make()
                        ->danger()
                        ->title('Free plan · invitations not available')
                        ->body('Inviting members requires the Starter or Pro plan.')
                        ->send();

                    $this->redirect(AccountBilling::getUrl(tenant: $team));

                    return;
                }

                $email = strtolower(trim($data['email']));

                if ($team->users()->where('email', $email)->exists()) {
                    Notification::make()
                        ->warning()
                        ->title('That user is already a member.')
                        ->send();

                    return;
                }

                if ($team->invitations()->where('email', $email)->whereNull('accepted_at')->exists()) {
                    Notification::make()
                        ->warning()
                        ->title('An invitation for that email already exists.')
                        ->send();

                    return;
                }

                Invitation::create([
                    'team_id' => $team->id,
                    'invited_by_user_id' => auth()->id(),
                    'email' => $email,
                ]);

                Notification::make()
                    ->success()
                    ->title('Invitation sent.')
                    ->send();

                $this->resetTable();
            });
    }

    private function revokeInvitationAction(): Action
    {
        return Action::make('revokeInvitation')
            ->label('Revoke')
            ->icon(Heroicon::OutlinedXMark)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'revoke-invitation-button'])
            ->requiresConfirmation()
            ->modalHeading('Revoke invitation?')
            ->modalSubmitActionLabel('Revoke')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'revoke-invitation-confirm']))
            ->action(function (Invitation $record): void {
                /** @var Team|null $team */
                $team = Filament::getTenant();

                if ($team === null || $record->team_id !== $team->id) {
                    return;
                }

                $record->delete();

                Notification::make()
                    ->success()
                    ->title('Invitation revoked.')
                    ->send();

                $this->resetTable();
            });
    }
}
