<?php

namespace App\Filament\Account;

use App\Actions\Accounts\CancelAccountDeletion;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PendingDeletion extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $slug = 'pending-deletion';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament-panels::pages.simple';

    protected static string $layout = 'filament-panels::components.layout.simple';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Medium;
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isDeleted() ?? false;
    }

    public function getTitle(): string
    {
        return 'Account scheduled for deletion';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function content(Schema $schema): Schema
    {
        $user = auth()->user();
        $graceDays = (int) config('account.deletion_grace_days');
        $remaining = max(0, $graceDays - (int) $user->deleted_at->diffInDays(now()));

        return $schema->components([
            Text::make('Your account will be deleted in '.$remaining.' days')
                ->size(TextSize::Large)
                ->weight('bold'),

            Text::make('All your data will be permanently removed once the timer runs out. Cancel now to keep your account, or sign out and decide later — you can come back any time before the deadline.')
                ->color('gray'),

            ActionsComponent::make([
                $this->cancelDeletionAction(),
                $this->signOutAction(),
            ]),
        ]);
    }

    public function cancelDeletionAction(): Action
    {
        return Action::make('cancelDeletion')
            ->label('Cancel deletion')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('primary')
            ->extraAttributes(['data-testid' => 'cancel-account-deletion'])
            ->requiresConfirmation()
            ->modalHeading('Cancel account deletion?')
            ->modalDescription('Your account stays active.')
            ->modalSubmitActionLabel('Yes, keep my account')
            ->modalSubmitAction(fn (?Action $action) => $action?->extraAttributes(['data-testid' => 'cancel-account-deletion-confirm']))
            ->action(function (): void {
                $user = auth()->user();

                (new CancelAccountDeletion)->handle($user);

                Notification::make()
                    ->success()
                    ->title('Account deletion cancelled.')
                    ->send();

                $this->redirect('/app');
            });
    }

    public function signOutAction(): Action
    {
        return Action::make('signOut')
            ->label('Sign out')
            ->icon(Heroicon::OutlinedArrowRightStartOnRectangle)
            ->color('gray')
            ->outlined()
            ->extraAttributes(['data-testid' => 'pending-deletion-sign-out'])
            ->action(function (): void {
                auth()->logout();
                request()->session()?->invalidate();
                request()->session()?->regenerateToken();

                $this->redirect('/app/login');
            });
    }
}
