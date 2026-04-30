<?php

namespace App\Filament\Widgets;

use App\Filament\Support\CategoryHeading;
use App\Models\Invitation;
use App\Models\Team;
use App\TeamRole;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class TeamInvitationsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(CategoryHeading::make('heroicon-o-envelope', 'primary', 'Invitations'))
            ->records(fn (): Collection => $this->getInvitations())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('email')
                            ->weight(FontWeight::Medium),
                        TextColumn::make('created_at')
                            ->state(fn (Invitation $record): string => 'Sent '.$record->created_at->diffForHumans())
                            ->color('gray')
                            ->size(TextSize::Small),
                    ]),
                    SelectColumn::make('role')
                        ->enum(TeamRole::class)
                        ->options(TeamRole::class)
                        ->selectablePlaceholder(false)
                        ->native(false)
                        ->extraAttributes(['data-testid' => 'invitation-role-select'])
                        ->afterStateUpdated(function ($state, $record) {
                            $role = TeamRole::tryFrom($state);
                            $record->update(['role' => $role]);

                            Notification::make()
                                ->success()
                                ->body('Role updated')
                                ->send();
                        })
                        ->grow(false),
                ]),
            ])
            ->headerActions([
                $this->inviteAction()->extraAttributes(['data-testid' => 'invite-button']),
            ])
            ->recordActions([
                ActionGroup::make([
                    $this->revokeInvitationAction(),
                ]),
            ])
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-envelope')
            ->emptyStateHeading('No pending invitations')
            ->emptyStateDescription('Invite someone by email to give them access to this team.')
            ->emptyStateActions([
                $this->inviteAction()
                    ->name('inviteFromEmptyState')
                    ->extraAttributes(['data-testid' => 'invite-button-empty'])
                    ->button()
                    ->outlined(),
            ]);
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

    private function inviteAction(): Action
    {
        return Action::make('invite')
            ->label('Invite member')
            ->icon(Heroicon::OutlinedUserPlus)
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedUserPlus)
            ->modalHeading('Invite a new team member')
            ->modalDescription('Enter their email and pick a role — they will see the invitation after signing in.')
            ->modalSubmitActionLabel('Send invitation')
            ->modalSubmitAction(fn (Action $action) => $action->extraAttributes(['data-testid' => 'invite-submit-button']))
            ->fillForm(['role' => TeamRole::Member->value])
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('teammate@example.com')
                    ->prefixIcon(Heroicon::OutlinedEnvelope)
                    ->extraInputAttributes(['data-testid' => 'invite-email']),
                Select::make('role')
                    ->label('Role')
                    ->enum(TeamRole::class)
                    ->options(TeamRole::class)
                    ->required()
                    ->native(false)
                    ->prefixIcon(Heroicon::OutlinedShieldCheck)
                    ->extraAttributes(['data-testid' => 'invite-role']),
            ])
            ->action(function (array $data): void {
                $team = Filament::getTenant();
                $email = strtolower(trim($data['email']));
                $role = $data['role'];

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
                    'role' => $role->value,
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
