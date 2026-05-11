<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;

class CustomLogin extends Login
{
    public function hasLogo(): bool
    {
        return false;
    }

    protected function getRememberFormComponent(): Component
    {
        return Hidden::make('remember')
            ->default(true);
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->extraInputAttributes(['data-testid' => 'login-email']);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->extraInputAttributes(['data-testid' => 'login-password']);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->extraAttributes(['data-testid' => 'login-submit']);
    }
}
