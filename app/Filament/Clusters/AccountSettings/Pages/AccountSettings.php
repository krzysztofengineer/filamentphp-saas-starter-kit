<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\Actions\ChangeAccountPassword;
use App\Actions\UpdateAccountProfile;
use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Filament\Support\CategoryHeading;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountSettings extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.clusters.account-settings.pages.account-settings';

    /** @var array<string, mixed> */
    public array $data = [
        'name' => '',
        'email' => '',
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public static function canAccess(): bool
    {
        return AccountSettingsCluster::canAccess();
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function getTitle(): string
    {
        return 'Account settings';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Account settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->heading(CategoryHeading::make('heroicon-o-user', 'primary', 'Your details'))
                    ->description('Your name shows on activity. Email is used to sign in and cannot be changed here.')
                    ->footerActions([
                        $this->saveAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon(Heroicon::OutlinedUser)
                            ->extraInputAttributes(['data-testid' => 'account-profile-name-input']),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                            ->extraInputAttributes(['data-testid' => 'account-profile-email-input']),
                    ]),

                Section::make()
                    ->heading(CategoryHeading::make('heroicon-o-lock-closed', 'primary', 'Change password'))
                    ->description('Set a new password. A good password is at least 8 characters.')
                    ->footerActions([
                        $this->changePasswordAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->prefixIcon(Heroicon::OutlinedKey)
                            ->extraInputAttributes(['data-testid' => 'account-password-current']),

                        TextInput::make('password')
                            ->label('New password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->minLength(8)
                            ->prefixIcon(Heroicon::OutlinedLockClosed)
                            ->extraInputAttributes(['data-testid' => 'account-password-new']),

                        TextInput::make('password_confirmation')
                            ->label('Repeat new password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->same('password')
                            ->prefixIcon(Heroicon::OutlinedLockClosed)
                            ->extraInputAttributes(['data-testid' => 'account-password-confirm']),
                    ]),
            ]);
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Save changes')
            ->icon(Heroicon::OutlinedCheck)
            ->extraAttributes(['data-testid' => 'account-profile-save'])
            ->action(function (): void {
                $data = $this->form->getState();

                /** @var User $user */
                $user = auth()->user();

                (new UpdateAccountProfile)->handle($user, ['name' => $data['name']]);

                Notification::make()
                    ->success()
                    ->title('Account saved.')
                    ->send();
            });
    }

    public function changePasswordAction(): Action
    {
        return Action::make('changePassword')
            ->label('Change password')
            ->icon(Heroicon::OutlinedCheck)
            ->extraAttributes(['data-testid' => 'account-password-save'])
            ->action(function (): void {
                $state = $this->form->getRawState();

                $current = $state['current_password'] ?? '';
                $new = $state['password'] ?? '';
                $confirmation = $state['password_confirmation'] ?? '';

                /** @var User $user */
                $user = auth()->user();

                if ($current === '' || ! Hash::check($current, $user->password)) {
                    throw ValidationException::withMessages([
                        'data.current_password' => 'Your current password is incorrect.',
                    ]);
                }

                if ($new === '' || strlen($new) < 8) {
                    throw ValidationException::withMessages([
                        'data.password' => 'The new password must be at least 8 characters.',
                    ]);
                }

                if ($new !== $confirmation) {
                    throw ValidationException::withMessages([
                        'data.password_confirmation' => 'The passwords do not match.',
                    ]);
                }

                (new ChangeAccountPassword)->handle($user, $new);

                $this->form->fill([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);

                Notification::make()
                    ->success()
                    ->title('Password changed.')
                    ->send();
            });
    }
}
