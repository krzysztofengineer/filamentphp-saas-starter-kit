<?php

namespace App\Filament\Pages;

use App\Actions\Teams\CreateTeamForUser;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class CreateTeam extends RegisterTenant
{
    protected static ?string $slug = 'new';

    public function getMaxWidth(): Width|string|null
    {
        return Width::Medium;
    }

    public static function getLabel(): string
    {
        return 'Create Team';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->extraAttributes(['data-testid' => 'create-team-logo']),

                TextInput::make('name')
                    ->label('Team name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->extraInputAttributes(['data-testid' => 'create-team-name']),
            ]);
    }

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()
            ->extraAttributes(['data-testid' => 'create-team-submit']);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = (new CreateTeamForUser)->handle(Auth::user(), $data['name']);

        if (! empty($data['logo'])) {
            $team->update(['logo' => $data['logo']]);
        }

        return $team;
    }
}
