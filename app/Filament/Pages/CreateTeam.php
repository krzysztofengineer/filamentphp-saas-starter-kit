<?php

namespace App\Filament\Pages;

use App\Models\Team;
use App\TeamRole;
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
                TextInput::make('name')
                    ->label('Team name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->extraInputAttributes(['data-testid' => 'create-team-name']),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);

        $team->users()->attach(Auth::user(), ['role' => TeamRole::Administrator->value]);

        Auth::user()->update(['current_team_id' => $team->id]);

        return $team;
    }
}
