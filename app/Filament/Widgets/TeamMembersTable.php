<?php

namespace App\Filament\Widgets;

use App\Actions\Teams\ChangeTeamRole;
use App\Actions\Teams\RemoveTeamMember;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class TeamMembersTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->header(fn () => view('components.table-section-header', [
                'icon' => 'heroicon-o-users',
                'heading' => 'Members',
                'actions' => $this->getTable()->getHeaderActions(),
            ]))
            ->records(fn (): Collection => $this->getMembers())
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->state(fn (User $record): string => Filament::getUserAvatarUrl($record))
                        ->circular()
                        ->grow(false)
                        ->size(40),
                    Stack::make([
                        Split::make([
                            TextColumn::make('name')
                                ->weight(FontWeight::Medium)
                                ->grow(false),
                            TextColumn::make('owner_badge')
                                ->state('Owner')
                                ->badge()
                                ->color('primary')
                                ->visible(fn (?User $record): bool => $record !== null && $this->isTeamOwner($record)),
                        ]),
                        TextColumn::make('email')
                            ->color('gray')
                            ->size(TextSize::Small),
                    ]),
                    SelectColumn::make('role')
                        ->state(fn (User $record): string => $record->pivot->role)
                        ->options(self::roleOptions())
                        ->selectablePlaceholder(false)
                        ->native(false)
                        ->disabled(fn (?User $record): bool => $record !== null && ! $this->canChangeRoleFor($record))
                        ->updateStateUsing(function (User $record, string $state): ?string {
                            $team = Filament::getTenant();

                            if ($team === null) {
                                return $record->pivot->role;
                            }

                            $newRole = TeamRole::from($state);

                            if ($this->wouldRemoveLastAdministrator($team, $record, $newRole)) {
                                Notification::make()
                                    ->danger()
                                    ->title('A team must have at least one administrator.')
                                    ->send();

                                return $record->pivot->role;
                            }

                            (new ChangeTeamRole)->handle($team, $record, $newRole);

                            Notification::make()
                                ->success()
                                ->title('Role updated.')
                                ->send();

                            return $newRole->value;
                        })
                        ->extraAttributes(['data-testid' => 'member-role-select'])
                        ->grow(false),
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    $this->removeMemberAction(),
                ]),
            ])
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('No members yet')
            ->emptyStateDescription('Invite people below.');
    }

    private function getMembers(): Collection
    {
        $team = Filament::getTenant();

        if ($team === null) {
            return collect();
        }

        return $team->users()
            ->orderBy('team_user.created_at')
            ->get()
            ->sortBy(fn (User $user): int => $this->isTeamOwner($user) ? 0 : 1)
            ->values();
    }

    private function removeMemberAction(): Action
    {
        return Action::make('removeMember')
            ->label('Remove')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'remove-member'])
            ->requiresConfirmation()
            ->modalHeading('Remove member?')
            ->modalDescription('They will lose access to this team.')
            ->modalSubmitActionLabel('Remove')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'remove-member-confirm']))
            ->visible(fn (User $record): bool => $this->canRemove($record))
            ->action(function (User $record): void {
                $team = Filament::getTenant();

                if ($team === null) {
                    return;
                }

                if ($this->wouldRemoveLastAdministrator($team, $record, null)) {
                    Notification::make()
                        ->danger()
                        ->title('A team must have at least one administrator.')
                        ->send();

                    return;
                }

                (new RemoveTeamMember)->handle($team, $record);

                Notification::make()
                    ->success()
                    ->title('Member removed.')
                    ->send();

                $this->resetTable();
            });
    }

    private function canChangeRoleFor(User $record): bool
    {
        $team = Filament::getTenant();
        $actor = auth()->user();

        if ($team === null || $actor === null) {
            return false;
        }

        return $actor->can('manageMembers', $team)
            && $record->id !== $actor->id
            && ! $this->isTeamOwner($record);
    }

    private function canRemove(User $record): bool
    {
        $team = Filament::getTenant();
        $actor = auth()->user();

        if ($team === null || $actor === null) {
            return false;
        }

        return $actor->can('manageMembers', $team)
            && $record->id !== $actor->id
            && ! $this->isTeamOwner($record);
    }

    private function isTeamOwner(User $record): bool
    {
        $team = Filament::getTenant();

        return $team?->user_id === $record->id;
    }

    private function wouldRemoveLastAdministrator(Team $team, User $target, ?TeamRole $newRole): bool
    {
        if ($target->cannot('update', $team)) {
            return false;
        }

        if ($newRole === TeamRole::Administrator) {
            return false;
        }

        return $team->administrators()->count() <= 1;
    }

    private static function roleOptions(): array
    {
        return collect(TeamRole::cases())
            ->mapWithKeys(fn (TeamRole $role): array => [$role->value => $role->label()])
            ->all();
    }
}
