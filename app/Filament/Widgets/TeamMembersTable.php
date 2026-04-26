<?php

namespace App\Filament\Widgets;

use App\Filament\Support\CategoryHeading;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
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
                            ->weight('bold'),
                        TextColumn::make('email')
                            ->color('gray')
                            ->size(TextSize::Small),
                    ]),
                    TextColumn::make('pivot.role')
                        ->badge()
                        ->color(fn (string $state): string => TeamRole::from($state)->color())
                        ->formatStateUsing(fn (string $state): string => TeamRole::from($state)->label())
                        ->grow(false),
                ]),
            ])
            ->recordActions([
                $this->removeMemberAction(),
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

        return $team->users()
            ->orderBy('users.name')
            ->get()
            ->sortBy(fn (User $user): int => TeamRole::from($user->pivot->role) === TeamRole::Owner ? 0 : 1)
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
            ->visible(fn (User $record): bool => TeamRole::from($record->pivot->role) !== TeamRole::Owner)
            ->action(function (User $record): void {
                /** @var Team|null $team */
                $team = Filament::getTenant();

                if ($team === null) {
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
}
