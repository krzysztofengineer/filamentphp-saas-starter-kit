<?php

namespace App\Filament\Clusters\TeamSettings\Pages;

use App\Actions\TransferTeamOwnership;
use App\Filament\Clusters\TeamSettings\TeamSettingsCluster;
use App\Filament\Support\CategoryHeading;
use App\Models\Team;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TeamAdvanced extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $cluster = TeamSettingsCluster::class;

    protected static ?string $slug = 'advanced';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.clusters.team-settings.pages.team-advanced';

    public static function canAccess(): bool
    {
        return TeamSettingsCluster::canAccess();
    }

    public function getTitle(): string
    {
        return 'Advanced';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Advanced';
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->heading(CategoryHeading::make('heroicon-o-arrows-right-left', 'primary', 'Transfer ownership'))
                ->description('Hand off the owner role to another team member. After the transfer you become a regular member and lose access to team settings.')
                ->footerActions([
                    $this->transferOwnershipAction(),
                ])
                ->footerActionsAlignment(Alignment::End),

            Section::make()
                ->heading(CategoryHeading::make('heroicon-o-trash', 'danger', 'Delete team'))
                ->description('Deleting this team permanently removes all of its data and member assignments. This cannot be undone.')
                ->footerActions([
                    $this->deleteTeamAction(),
                ])
                ->footerActionsAlignment(Alignment::End),
        ]);
    }

    public function transferOwnershipAction(): Action
    {
        /** @var Team $team */
        $team = Filament::getTenant();

        $candidates = $team->members()->orderBy('name')->get();

        return Action::make('transferOwnership')
            ->label('Transfer ownership')
            ->icon(Heroicon::OutlinedArrowsRightLeft)
            ->color('primary')
            ->outlined()
            ->extraAttributes(['data-testid' => 'transfer-ownership-button'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedArrowsRightLeft)
            ->modalHeading('Transfer ownership')
            ->modalDescription('The new owner will take over team settings, members, and deletion. You will lose those permissions.')
            ->modalSubmitActionLabel('Transfer')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'transfer-ownership-confirm']))
            ->disabled($candidates->isEmpty())
            ->schema([
                Select::make('new_owner_id')
                    ->label('New owner')
                    ->options($candidates->pluck('name', 'id'))
                    ->required()
                    ->prefixIcon(Heroicon::OutlinedUser)
                    ->extraAttributes(['data-testid' => 'transfer-ownership-select']),
            ])
            ->action(function (array $data): void {
                /** @var Team $team */
                $team = Filament::getTenant();
                $currentOwner = auth()->user();
                $newOwner = User::find($data['new_owner_id']);

                if (! $newOwner || ! $team->members()->whereKey($newOwner->id)->exists()) {
                    Notification::make()->danger()->title('That user is not a team member.')->send();

                    return;
                }

                (new TransferTeamOwnership)($team, $currentOwner, $newOwner);

                Notification::make()
                    ->success()
                    ->title('Ownership transferred to '.$newOwner->name.'.')
                    ->send();

                $this->redirect(Filament::getUrl($team));
            });
    }

    public function deleteTeamAction(): Action
    {
        /** @var Team $team */
        $team = Filament::getTenant();

        return Action::make('deleteTeam')
            ->label('Delete team')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'delete-team-button'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading('Delete team '.$team->name)
            ->modalDescription('All team data and invitations will be permanently deleted. To confirm, type the team name below.')
            ->schema([
                TextInput::make('name_confirmation')
                    ->label('Type the team name to confirm')
                    ->placeholder($team->name)
                    ->required()
                    ->prefixIcon(Heroicon::OutlinedExclamationTriangle)
                    ->extraInputAttributes(['data-testid' => 'delete-team-name-input'])
                    ->rule('in:'.$team->name),
            ])
            ->modalSubmitAction(fn (?Action $action) => $action
                ?->label('Delete '.$team->name)
                ->color('danger')
                ->extraAttributes(['data-testid' => 'delete-team-confirm']))
            ->modalCancelActionLabel('Close')
            ->action(function (array $data): void {
                /** @var Team $team */
                $team = Filament::getTenant();
                /** @var User $user */
                $user = auth()->user();

                if (($data['name_confirmation'] ?? null) !== $team->name) {
                    Notification::make()->danger()->title('The name does not match.')->send();

                    return;
                }

                $nextTeam = $user->teams()
                    ->where('teams.id', '!=', $team->id)
                    ->orderBy('teams.id')
                    ->first();

                $team->delete();

                Notification::make()
                    ->success()
                    ->title('Team deleted.')
                    ->send();

                if ($nextTeam !== null) {
                    $user->update(['current_team_id' => $nextTeam->id]);
                    $this->redirect(Filament::getUrl($nextTeam));

                    return;
                }

                $this->redirect('/app/new');
            });
    }
}
