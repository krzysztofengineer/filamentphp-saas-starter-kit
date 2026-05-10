<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\Actions\Teams\AcceptTeamInvitation;
use App\Actions\Teams\DeclineTeamInvitation;
use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Models\TeamInvitation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class TeamInvitations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'team-invitations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return 'Team invitations';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Team invitations';
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->header(fn () => view('components.table-section-header', [
                'icon' => 'heroicon-o-envelope-open',
                'heading' => 'Invitations for you',
                'actions' => $this->getTable()->getHeaderActions(),
            ]))
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
            ->emptyStateHeading('No invitations');
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
                $user = auth()->user();

                if (strtolower($user->email) !== strtolower($record->email) || $record->isAccepted()) {
                    Notification::make()->danger()->title('That invitation is no longer valid.')->send();

                    return;
                }

                (new AcceptTeamInvitation)->handle($record, $user);

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
                $user = auth()->user();

                if (strtolower($user->email) !== strtolower($record->email) || $record->isAccepted()) {
                    return;
                }

                (new DeclineTeamInvitation)->handle($record);

                Notification::make()->success()->title('Invitation declined.')->send();

                $this->resetTable();
            });
    }
}
