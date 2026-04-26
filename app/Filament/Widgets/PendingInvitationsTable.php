<?php

namespace App\Filament\Widgets;

use App\Actions\AcceptInvitation;
use App\Filament\Clusters\AccountSettings\Pages\AccountBilling;
use App\Filament\Support\CategoryHeading;
use App\Models\Invitation;
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
use Illuminate\Support\Collection;

class PendingInvitationsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(CategoryHeading::make('heroicon-o-envelope-open', 'primary', 'Invitations for you'))
            ->records(fn (): Collection => $this->getInvitations())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('team.name')
                            ->weight('bold'),
                        TextColumn::make('invited_by')
                            ->state(fn (Invitation $record): string => 'Invited by '.($record->invitedBy?->name ?? $record->invitedBy?->email ?? '—'))
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

    private function getInvitations(): Collection
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return Invitation::query()
            ->with(['team', 'invitedBy'])
            ->whereNull('accepted_at')
            ->where('email', strtolower($user->email))
            ->orderBy('created_at')
            ->get();
    }

    private function acceptAction(): Action
    {
        return Action::make('accept')
            ->label('Accept')
            ->icon(Heroicon::OutlinedCheck)
            ->color('primary')
            ->button()
            ->extraAttributes(['data-testid' => 'invitation-accept'])
            ->action(function (Invitation $record): void {
                /** @var User $user */
                $user = auth()->user();

                if (strtolower($user->email) !== strtolower($record->email) || $record->isAccepted()) {
                    Notification::make()->danger()->title('That invitation is no longer valid.')->send();

                    return;
                }

                if (! $user->canAddMoreTeams()) {
                    Notification::make()
                        ->danger()
                        ->title('Free plan only allows one team.')
                        ->body('Upgrade to Starter or Pro to accept this invitation.')
                        ->send();

                    $tenant = $user->teams()->first();

                    if ($tenant !== null) {
                        $this->redirect(AccountBilling::getUrl(tenant: $tenant));
                    }

                    return;
                }

                (new AcceptInvitation)($record, $user);
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
            ->requiresConfirmation()
            ->modalHeading('Decline invitation?')
            ->action(function (Invitation $record): void {
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
