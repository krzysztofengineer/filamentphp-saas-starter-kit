<?php

namespace App\Filament\Widgets;

use App\Actions\AcceptTeamInvitation;
use App\Filament\Support\CategoryHeading;
use App\Models\TeamInvitation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingTeamInvitationsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(CategoryHeading::make('heroicon-o-envelope-open', 'primary', 'Invitations for you'))
            ->query(fn () => TeamInvitation::query()->where('email', strtolower(auth()->user()->email)))
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('team.name')
                            ->weight('bold'),
                        TextColumn::make('invited_by')
                            ->state(fn (TeamInvitation $record): string => 'Invited by '.($record->user?->name ?? $record->user?->email ?? '—'))
                            ->color('gray')
                            ->size(TextSize::Small),
                    ]),
                ]),
            ])
            ->recordActions([
                $this->acceptAction(),
                $this->declineAction(),
            ])
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-envelope-open')
            ->emptyStateHeading('No invitations')
            ->emptyStateDescription('You have no pending team invitations.');
    }

    private function acceptAction(): Action
    {
        return Action::make('accept')
            ->label('Accept')
            ->icon(Heroicon::OutlinedCheck)
            ->color('primary')
            ->button()
            ->extraAttributes(['data-testid' => 'invitation-accept'])
            ->action(function (TeamInvitation $record): void {
                /** @var User $user */
                $user = auth()->user();

                if (strtolower($user->email) !== strtolower($record->email) || $record->isAccepted()) {
                    Notification::make()->danger()->title('That invitation is no longer valid.')->send();

                    return;
                }

                (new AcceptTeamInvitation)($record, $user);
                $user->update(['current_team_id' => $record->team_id]);

                Notification::make()->success()->title('Joined the team.')->send();

                $this->redirect(Filament::getUrl($record->team));
            });
    }

    private function declineAction(): Action
    {
        return Action::make('decline')
            ->label('Decline')
            ->icon(Heroicon::OutlinedXMark)
            ->color('gray')
            ->extraAttributes(['data-testid' => 'invitation-decline'])
            ->requiresConfirmation()
            ->modalHeading('Decline invitation?')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'invitation-decline-confirm']))
            ->action(function (TeamInvitation $record): void {
                /** @var User $user */
                $user = auth()->user();

                if (strtolower($user->email) !== strtolower($record->email) || $record->isAccepted()) {
                    return;
                }

                $record->delete();

                Notification::make()->success()->title('Invitation declined.')->send();

                $this->resetTable();
            });
    }
}
