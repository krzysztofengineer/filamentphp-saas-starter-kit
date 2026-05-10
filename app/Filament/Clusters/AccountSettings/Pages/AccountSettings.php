<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\Actions\Accounts\ChangeAccountPassword;
use App\Actions\Accounts\UpdateAccountProfile;
use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountSettings extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?int $navigationSort = 1;

    public array $data = [
        'name' => '',
        'email' => '',
        'avatar' => null,
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
        $user = auth()->user();

        $this->content->fill([
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
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

    public function content(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->heading('Your details')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->description('Email is used to sign in and cannot be changed here.')
                    ->footerActions([
                        $this->saveAction(),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->avatar()
                            ->image()
                            ->disk('user-avatars')
                            ->directory('')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->circleCropper()
                            ->extraAttributes(['data-testid' => 'account-profile-avatar']),

                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon(Heroicon::OutlinedUser)
                            ->extraInputAttributes(['data-testid' => 'account-profile-name']),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                            ->extraInputAttributes(['data-testid' => 'account-profile-email']),
                    ]),

                Section::make()
                    ->heading('Change password')
                    ->icon('heroicon-o-lock-closed')
                    ->iconColor('primary')
                    ->description('Minimum 8 characters.')
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
                $data = $this->content->getState();

                $user = auth()->user();

                (new UpdateAccountProfile)->handle($user, [
                    'name' => $data['name'],
                    'avatar' => $data['avatar'] ?? null,
                ]);

                Notification::make()
                    ->success()
                    ->title('Account saved.')
                    ->send();

                $this->dispatch('refresh-topbar');
                $this->dispatch('refresh-sidebar');
            });
    }

    public function changePasswordAction(): Action
    {
        return Action::make('changePassword')
            ->label('Change password')
            ->icon(Heroicon::OutlinedCheck)
            ->extraAttributes(['data-testid' => 'account-password-save'])
            ->action(function (): void {
                $state = $this->content->getRawState();

                $current = $state['current_password'] ?? '';
                $new = $state['password'] ?? '';

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

                if ($new !== ($state['password_confirmation'] ?? '')) {
                    throw ValidationException::withMessages([
                        'data.password_confirmation' => 'The passwords do not match.',
                    ]);
                }

                (new ChangeAccountPassword)->handle($user, $new);

                $this->content->fill([
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
