<?php

namespace App\Filament\Widgets;

use App\Actions\Teams\InviteToTeam;
use App\Actions\Teams\RevokeTeamInvitation;
use App\Actions\Teams\UpdateTeamInvitationRole;
use App\Enums\TeamRole;
use App\Models\TeamInvitation;
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
            ->header(fn () => view('components.table-section-header', [
                'icon' => 'heroicon-o-envelope',
                'heading' => 'Invitations',
                'actions' => $this->getTable()->getHeaderActions(),
            ]))
            ->records(fn (): Collection => $this->getInvitations())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('email')
                            ->weight(FontWeight::Medium),
                        TextColumn::make('created_at')
                            ->state(fn (TeamInvitation $record): string => 'Sent '.$record->created_at->diffForHumans())
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
                            (new UpdateTeamInvitationRole)->handle($record, $role);

                            Notification::make()
                                ->success()
                                ->body('Role updated')
                                ->send();
                        })
                        ->grow(false),
                ]),
            ])
            ->headerActions(array_filter([
                $this->canInvite() ? $this->inviteAction()->extraAttributes(['data-testid' => 'invite']) : null,
            ]))
            ->recordActions([
                ActionGroup::make([
                    $this->revokeInvitationAction(),
                ]),
            ])
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-envelope')
            ->emptyStateHeading('No pending invitations')
            ->emptyStateDescription($this->canInvite()
                ? 'Invite someone by email.'
                : 'Personal teams can\'t have members. Create a team first.')
            ->emptyStateActions(array_filter([
                $this->canInvite() ? $this->inviteAction()
                    ->name('inviteFromEmptyState')
                    ->extraAttributes(['data-testid' => 'invite-empty'])
                    ->button()
                    ->outlined() : $this->createTeamAction(),
            ]));
    }

    private function createTeamAction(): Action
    {
        return Action::make('createTeam')
            ->label('Create a team')
            ->icon(Heroicon::OutlinedPlus)
            ->color('primary')
            ->button()
            ->outlined()
            ->extraAttributes(['data-testid' => 'create-team-from-personal'])
            ->url('/app/new');
    }

    private function canInvite(): bool
    {
        return Filament::getTenant()?->isPersonal() === false;
    }

    private function getInvitations(): Collection
    {
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
            ->modalHeading('Invite member')
            ->modalDescription('Enter their email and pick a role.')
            ->modalSubmitActionLabel('Send invitation')
            ->modalSubmitAction(fn (Action $action) => $action->extraAttributes(['data-testid' => 'invite-submit']))
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

                (new InviteToTeam)->handle($team, auth()->user(), $email, $role);

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
            ->extraAttributes(['data-testid' => 'revoke-invitation'])
            ->requiresConfirmation()
            ->modalHeading('Revoke invitation?')
            ->modalSubmitActionLabel('Revoke')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'revoke-invitation-confirm']))
            ->action(function (TeamInvitation $record): void {
                $team = Filament::getTenant();

                if ($team === null || $record->team_id !== $team->id) {
                    return;
                }

                (new RevokeTeamInvitation)->handle($record);

                Notification::make()
                    ->success()
                    ->title('Invitation revoked.')
                    ->send();

                $this->resetTable();
            });
    }
}
