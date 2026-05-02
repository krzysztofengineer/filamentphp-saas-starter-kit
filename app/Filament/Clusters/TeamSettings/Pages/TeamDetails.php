<?php

namespace App\Filament\Clusters\TeamSettings\Pages;

use App\Actions\UpdateTeamProfile;
use App\Filament\Clusters\TeamSettings\TeamSettingsCluster;
use App\Filament\Support\CategoryHeading;
use App\Models\Team;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TeamDetails extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $cluster = TeamSettingsCluster::class;

    protected static ?string $slug = 'profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.clusters.team-settings.pages.team-profile';

    /** @var array<string, mixed> */
    public array $data = [];

    public static function canAccess(): bool
    {
        return TeamSettingsCluster::canAccess();
    }

    public function mount(): void
    {
        /** @var Team $team */
        $team = Filament::getTenant();

        $this->form->fill([
            'name' => $team->name,
        ]);
    }

    public function getTitle(): string
    {
        return 'Team details';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Team details';
    }

    public function form(Schema $schema): Schema
    {
        /** @var Team $team */
        $team = Filament::getTenant();

        return $schema
            ->model($team)
            ->statePath('data')
            ->components([
                Section::make()
                    ->heading(CategoryHeading::make('heroicon-o-identification', 'primary', 'Team details'))
                    ->description('Edit basic team information. Changes are visible immediately to all members.')
                    ->footerActions([
                        $this->saveAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->extraInputAttributes(['data-testid' => 'team-details-name'])
                            ->prefixIcon(Heroicon::OutlinedBuildingOffice2),
                    ]),
            ]);
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Save changes')
            ->icon(Heroicon::OutlinedCheck)
            ->extraAttributes(['data-testid' => 'team-details-save'])
            ->action(function (): void {
                $data = $this->form->getState();

                /** @var Team $team */
                $team = Filament::getTenant();

                (new UpdateTeamProfile)->handle($team, ['name' => $data['name']]);

                Notification::make()
                    ->success()
                    ->title('Team saved.')
                    ->send();
            });
    }
}
