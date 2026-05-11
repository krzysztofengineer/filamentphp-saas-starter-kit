<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Register;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class CustomRegister extends Register
{
    public function hasLogo(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getTermsFormComponent(),
            $this->getPrivacyFormComponent(),
        ]);
    }

    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->label('Name')
            ->extraInputAttributes(['data-testid' => 'register-name']);
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->extraInputAttributes(['data-testid' => 'register-email']);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->extraInputAttributes(['data-testid' => 'register-password']);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->extraInputAttributes(['data-testid' => 'register-password-confirm']);
    }

    protected function getTermsFormComponent(): Component
    {
        return Checkbox::make('terms')
            ->label(new HtmlString(
                'I accept the <a href="/terms" target="_blank" rel="noopener" class="font-bold text-primary-600 underline-offset-2 hover:underline">Terms of Service</a>.'
            ))
            ->required()
            ->accepted()
            ->validationMessages([
                'accepted' => 'You must accept the terms of service to create an account.',
                'required' => 'You must accept the terms of service to create an account.',
            ])
            ->default(false)
            ->dehydrated(false)
            ->extraAttributes(['data-testid' => 'register-terms']);
    }

    protected function getPrivacyFormComponent(): Component
    {
        return Checkbox::make('privacy')
            ->label(new HtmlString(
                'I have read the <a href="/privacy" target="_blank" rel="noopener" class="font-bold text-primary-600 underline-offset-2 hover:underline">Privacy Policy</a> and consent to the processing of my data as described.'
            ))
            ->required()
            ->accepted()
            ->validationMessages([
                'accepted' => 'You must accept the privacy policy to create an account.',
                'required' => 'You must accept the privacy policy to create an account.',
            ])
            ->default(false)
            ->dehydrated(false)
            ->extraAttributes(['data-testid' => 'register-privacy']);
    }

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()
            ->extraAttributes(['data-testid' => 'register-submit']);
    }
}
