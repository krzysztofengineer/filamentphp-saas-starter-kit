<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Filament\Support\CategoryHeading;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AccountAdvanced extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'advanced';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.clusters.account-settings.pages.account-advanced';

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
                ->heading(CategoryHeading::make('heroicon-o-trash', 'danger', 'Delete account'))
                ->description('Your account will be marked for deletion and permanently removed after 30 days, including all of your data. Before doing this, leave or delete any teams you administer. You can only delete your account once you no longer administer any teams.')
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
            ->extraAttributes(['data-testid' => 'delete-account-button'])
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedExclamationTriangle)
            ->modalIconColor('danger')
            ->modalHeading('Delete account')
            ->modalDescription(function (): string {
                /** @var User $user */
                $user = auth()->user();

                if ($user->administeredTeams()->exists()) {
                    return 'Leave or delete the teams you administer first.';
                }

                return 'Your account will be permanently deleted after 30 days. You can undo this by signing back in within that window.';
            })
            ->modalSubmitAction(function (?Action $action) {
                /** @var User $user */
                $user = auth()->user();

                if ($user->administeredTeams()->exists()) {
                    return $action?->hidden();
                }

                return $action
                    ?->label('Delete account')
                    ->color('danger')
                    ->extraAttributes(['data-testid' => 'delete-account-confirm']);
            })
            ->modalCancelActionLabel('Close')
            ->action(function (): void {
                /** @var User $user */
                $user = auth()->user();

                if ($user->administeredTeams()->exists()) {
                    return;
                }

                $user->update(['scheduled_for_deletion_at' => now()]);

                auth()->logout();
                request()->session()?->invalidate();
                request()->session()?->regenerateToken();

                $this->redirect('/app/login');
            });
    }
}
