<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\Actions\Accounts\ScheduleAccountDeletion;
use App\Filament\Account\PendingDeletion;
use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AccountAdvanced extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'advanced';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return AccountSettingsCluster::canAccess();
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
                ->heading('Delete account')
                ->icon('heroicon-o-trash')
                ->iconColor('danger')
                ->description('Your account will be marked for deletion and permanently removed after '.config('account.deletion_grace_days').' days, including all of your data and any team you solely own. If you administer a team with other members, leave it or transfer ownership first.')
                ->footerActions([
                    $this->deleteAccountAction(),
                ])
                ->footerActionsAlignment(Alignment::End),
        ]);
    }

    public function deleteAccountAction(): Action
    {
        return Action::make('deleteAccount')
            ->label('Delete my account')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->extraAttributes(['data-testid' => 'delete-account'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading('Delete account')
            ->modalDescription(function (): string {
                if (auth()->user()->administersOthers()) {
                    return 'You administer a team with other members. Leave it or transfer ownership before deleting your account.';
                }

                return 'Your account will be permanently deleted after '.config('account.deletion_grace_days').' days. You can undo this by signing back in within that window.';
            })
            ->modalSubmitAction(function (?Action $action) {
                if (auth()->user()->administersOthers()) {
                    return $action?->hidden();
                }

                return $action
                    ?->label('Delete account')
                    ->color('danger')
                    ->extraAttributes(['data-testid' => 'delete-account-confirm']);
            })
            ->modalCancelActionLabel('Close')
            ->action(function (): void {
                $user = auth()->user();

                if ($user->administersOthers()) {
                    return;
                }

                (new ScheduleAccountDeletion)->handle($user);

                Notification::make()
                    ->success()
                    ->title('Account scheduled for deletion.')
                    ->send();

                $this->redirect(PendingDeletion::getUrl());
            });
    }
}
