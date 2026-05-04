<?php

namespace App\Filament\Widgets;

use App\Actions\CancelAccountDeletion;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;

class PendingDeletionBanner extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'filament.widgets.pending-deletion-banner';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isDeleted() ?? false;
    }

    public function getRemainingDays(): int
    {
        $user = auth()->user();

        $graceDays = (int) config('account.deletion_grace_days');

        return max(0, $graceDays - (int) $user->deleted_at->diffInDays(now()));
    }

    public function cancelDeletionAction(): Action
    {
        return Action::make('cancelDeletion')
            ->label('Cancel deletion')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('primary')
            ->extraAttributes(['data-testid' => 'cancel-account-deletion'])
            ->action(function (): void {
                $user = auth()->user();

                (new CancelAccountDeletion)->handle($user);

                Notification::make()
                    ->success()
                    ->title('Account deletion cancelled.')
                    ->send();
            });
    }
}
