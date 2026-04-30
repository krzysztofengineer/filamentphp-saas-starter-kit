<?php

namespace App\Filament\Widgets;

use App\Filament\Support\CategoryHeading;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
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
            ->heading(CategoryHeading::make('heroicon-o-users', 'primary', 'Members'))
            ->records(fn (): Collection => $this->getMembers())
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->state(fn (User $record): ?string => $record->getFilamentAvatarUrl())
                        ->circular()
                        ->grow(false)
                        ->size(40),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Medium),
                        TextColumn::make('email')
                            ->color('gray')
                            ->size(TextSize::Small),
                    ]),
                    TextColumn::make('owner_badge')
                        ->state('Owner')
                        ->badge()
                        ->color('primary')
                        ->visible(fn (?User $record): bool => $record !== null && $this->isTeamOwner($record))
                        ->grow(false),
                    SelectColumn::make('role')
                        ->state(fn (User $record): string => $record->pivot->role)
                        ->options(self::roleOptions())
                        ->selectablePlaceholder(false)
                        ->native(false)
                        ->visible(fn (?User $record): bool => $record !== null && ! $this->isTeamOwner($record))
                        ->disabled(fn (User $record): bool => ! $this->canChangeRoleFor($record))
                        ->updateStateUsing(function (User $record, string $state): ?string {
                            /** @var Team|null $team */
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

                            $team->users()->updateExistingPivot($record->id, ['role' => $newRole->value]);

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
            ->emptyStateDescription('Invite people below to collaborate on this team.');
    }

    private function getMembers(): Collection
    {
        /** @var Team|null $team */
        $team = Filament::getTenant();

        if ($team === null) {
            return collect();
        }

        $rolePriority = [
            TeamRole::Administrator->value => 0,
            TeamRole::Member->value => 2,
        ];

        return $team->users()
            ->orderBy('users.name')
            ->get()
            ->sortBy(fn (User $user): int => $rolePriority[$user->pivot->role] ?? 99)
            ->values();
    }

    private function removeMemberAction(): Action
    {
        return Action::make('removeMember')
            ->label('Remove')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'remove-member-button'])
            ->requiresConfirmation()
            ->modalHeading('Remove member?')
            ->modalDescription('They will lose access to this team.')
            ->modalSubmitActionLabel('Remove')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'remove-member-confirm']))
            ->visible(fn (User $record): bool => $this->canRemove($record))
            ->action(function (User $record): void {
                /** @var Team|null $team */
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

                $team->users()->detach($record->id);

                if ($record->current_team_id === $team->id) {
                    $record->update(['current_team_id' => null]);
                }

                Notification::make()
                    ->success()
                    ->title('Member removed.')
                    ->send();

                $this->resetTable();
            });
    }

    private function canChangeRoleFor(User $record): bool
    {
        /** @var Team|null $team */
        $team = Filament::getTenant();
        /** @var User|null $actor */
        $actor = auth()->user();

        if ($team === null || $actor === null) {
            return false;
        }

        return $team->isAdministeredBy($actor)
            && $record->id !== $actor->id
            && ! $this->isTeamOwner($record);
    }

    private function canRemove(User $record): bool
    {
        /** @var Team|null $team */
        $team = Filament::getTenant();
        /** @var User|null $actor */
        $actor = auth()->user();

        if ($team === null || $actor === null) {
            return false;
        }

        return $team->canBeManagedBy($actor)
            && $record->id !== $actor->id
            && ! $this->isTeamOwner($record);
    }

    private function isTeamOwner(User $record): bool
    {
        /** @var Team|null $team */
        $team = Filament::getTenant();

        return $team?->user_id === $record->id;
    }

    private function wouldRemoveLastAdministrator(Team $team, User $target, ?TeamRole $newRole): bool
    {
        $currentRole = $team->roleFor($target);

        if ($currentRole !== TeamRole::Administrator) {
            return false;
        }

        if ($newRole === TeamRole::Administrator) {
            return false;
        }

        return $team->administrators()->count() <= 1;
    }

    /**
     * @return array<string, string>
     */
    private static function roleOptions(): array
    {
        return collect(TeamRole::cases())
            ->mapWithKeys(fn (TeamRole $role): array => [$role->value => $role->label()])
            ->all();
    }
}
