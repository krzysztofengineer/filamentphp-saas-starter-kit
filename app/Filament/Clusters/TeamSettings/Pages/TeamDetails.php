<?php

namespace App\Filament\Clusters\TeamSettings\Pages;

use App\Actions\Teams\DeleteTeam;
use App\Actions\Teams\LeaveTeam as LeaveTeamAction;
use App\Actions\Teams\TransferTeamOwnership;
use App\Actions\Teams\UpdateTeamProfile;
use App\Filament\Clusters\TeamSettings\TeamSettingsCluster;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TeamDetails extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $cluster = TeamSettingsCluster::class;

    protected static ?string $slug = 'profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    public array $data = [];

    public static function canAccess(): bool
    {
        return TeamSettingsCluster::canAccess();
    }

    public function mount(): void
    {
        $team = Filament::getTenant();

        $this->content->fill([
            'name' => $team->name,
            'logo' => $team->logo,
        ]);
    }

    public function getTitle(): string
    {
        return 'Team settings';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Team settings';
    }

    public function content(Schema $schema): Schema
    {
        $team = Filament::getTenant();
        $user = auth()->user();
        $canManage = TeamSettingsCluster::canManage();
        $isOwner = $team->user_id === $user->id;

        return $schema
            ->model($team)
            ->statePath('data')
            ->components([
                Section::make()
                    ->heading('Team profile')
                    ->icon('heroicon-o-identification')
                    ->iconColor('primary')
                    ->description('Visible to all members.')
                    ->visible($canManage)
                    ->footerActions([
                        $this->saveAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->avatar()
                            ->image()
                            ->disk('team-logos')
                            ->directory('')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->circleCropper()
                            ->extraAttributes(['data-testid' => 'team-details-logo']),

                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->extraInputAttributes(['data-testid' => 'team-details-name'])
                            ->prefixIcon(Heroicon::OutlinedBuildingOffice2),
                    ]),

                Section::make()
                    ->heading('Transfer ownership')
                    ->icon('heroicon-o-arrows-right-left')
                    ->iconColor('primary')
                    ->description('Promote another member to Administrator and demote yourself to Member.')
                    ->visible($canManage)
                    ->footerActions([
                        $this->transferOwnershipAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End),

                Section::make()
                    ->heading('Delete team')
                    ->icon('heroicon-o-trash')
                    ->iconColor('danger')
                    ->description('Permanent. Removes the team and all member assignments.')
                    ->visible($canManage)
                    ->footerActions([
                        $this->deleteTeamAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End),

                Section::make()
                    ->heading('Leave team')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->iconColor('danger')
                    ->description('The team owner can re-invite you afterwards.')
                    ->visible(! $isOwner && ! $team->isPersonal())
                    ->footerActions([
                        $this->leaveTeamAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End),
            ]);
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Save changes')
            ->icon(Heroicon::OutlinedCheck)
            ->extraAttributes(['data-testid' => 'team-details-save'])
            ->action(function (): void {
                $data = $this->content->getState();

                $team = Filament::getTenant();

                $this->dispatch('refresh-topbar');
                $this->dispatch('refresh-sidebar');

                (new UpdateTeamProfile)->handle($team, [
                    'name' => $data['name'],
                    'logo' => $data['logo'] ?? null,
                ]);

                Notification::make()
                    ->success()
                    ->title('Team saved.')
                    ->send();
            });
    }

    public function transferOwnershipAction(): Action
    {
        $team = Filament::getTenant();
        $actor = auth()->user();

        $candidates = $team->users()
            ->where('users.id', '!=', $actor?->id)
            ->orderBy('users.name')
            ->get();

        $disabledTooltip = match (true) {
            $team->isPersonal() => 'Personal teams cannot be transferred.',
            $candidates->isEmpty() => 'Invite another member first — there is no one to transfer ownership to.',
            default => null,
        };

        return Action::make('transferOwnership')
            ->label('Transfer ownership')
            ->icon(Heroicon::OutlinedArrowsRightLeft)
            ->color('primary')
            ->extraAttributes(['data-testid' => 'transfer-ownership'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedArrowsRightLeft)
            ->modalHeading('Transfer ownership')
            ->modalDescription('You will be demoted to Member.')
            ->modalSubmitActionLabel('Transfer')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'transfer-ownership-confirm']))
            ->disabled($disabledTooltip !== null)
            ->tooltip($disabledTooltip)
            ->schema([
                Select::make('new_admin_id')
                    ->label('New administrator')
                    ->options($candidates->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->prefixIcon(Heroicon::OutlinedUser)
                    ->extraAttributes(['data-testid' => 'transfer-ownership-select']),
            ])
            ->action(function (array $data): void {
                $team = Filament::getTenant();
                $actor = auth()->user();
                $newAdmin = User::find($data['new_admin_id']);

                if (! $newAdmin || ! $team->users()->whereKey($newAdmin->id)->exists()) {
                    Notification::make()->danger()->title('That user is not a team member.')->send();

                    return;
                }

                if ($actor->cannot('update', $team)) {
                    Notification::make()->danger()->title('Only administrators can transfer ownership.')->send();

                    return;
                }

                (new TransferTeamOwnership)->handle($team, $actor, $newAdmin);

                Notification::make()
                    ->success()
                    ->title('Ownership transferred to '.$newAdmin->name.'.')
                    ->send();

                $this->redirect(Filament::getUrl($team));
            });
    }

    public function deleteTeamAction(): Action
    {
        $team = Filament::getTenant();

        if ($team->isPersonal()) {
            return Action::make('deleteTeam')
                ->label('Delete team')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->extraAttributes(['data-testid' => 'delete-team'])
                ->disabled()
                ->tooltip('Personal teams can only be removed by deleting your account.');
        }

        return Action::make('deleteTeam')
            ->label('Delete team')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'delete-team'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading('Delete team '.$team->name)
            ->modalDescription('Type the team name to confirm.')
            ->schema([
                TextInput::make('name_confirmation')
                    ->label('Type the team name to confirm')
                    ->placeholder($team->name)
                    ->required()
                    ->prefixIcon(Heroicon::OutlinedExclamationTriangle)
                    ->extraInputAttributes(['data-testid' => 'delete-team-name'])
                    ->rule('in:'.$team->name),
            ])
            ->modalSubmitAction(fn (?Action $action) => $action
                ?->label('Delete '.$team->name)
                ->color('danger')
                ->extraAttributes(['data-testid' => 'delete-team-confirm']))
            ->modalCancelActionLabel('Close')
            ->action(function (array $data): void {
                $team = Filament::getTenant();
                $user = auth()->user();

                if (($data['name_confirmation'] ?? null) !== $team->name) {
                    Notification::make()->danger()->title('The name does not match.')->send();

                    return;
                }

                $nextTeam = (new DeleteTeam)->handle($team, $user);

                Notification::make()
                    ->success()
                    ->title('Team deleted.')
                    ->send();

                if ($nextTeam !== null) {
                    $this->redirect(Filament::getUrl($nextTeam));

                    return;
                }

                $this->redirect('/app/new');
            });
    }

    public function leaveTeamAction(): Action
    {
        $team = Filament::getTenant();

        return Action::make('leaveTeam')
            ->label('Leave team')
            ->icon(Heroicon::OutlinedArrowLeftStartOnRectangle)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'leave-team'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading('Leave '.$team->name.'?')
            ->modalDescription('You will lose access immediately. The team owner can re-invite you.')
            ->modalSubmitActionLabel('Leave team')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'leave-team-confirm']))
            ->modalCancelActionLabel('Close')
            ->action(function (): void {
                $team = Filament::getTenant();
                $user = auth()->user();

                $isSoleAdmin = $team->administrators()->count() === 1
                    && $user->can('update', $team);
                $hasOtherMembers = $team->members()->where('users.id', '!=', $user->id)->exists();

                if ($isSoleAdmin && $hasOtherMembers) {
                    Notification::make()
                        ->danger()
                        ->title('Transfer ownership first.')
                        ->body('You are the only administrator of this team. Promote another member or transfer ownership before leaving.')
                        ->send();

                    return;
                }

                (new LeaveTeamAction)->handle($team, $user);

                Notification::make()->success()->title('You left '.$team->name.'.')->send();

                $this->redirect('/app/new');
            });
    }
}
