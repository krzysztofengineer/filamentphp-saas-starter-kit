<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;

class PendingDeletionBanner extends Widget implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'filament.widgets.pending-deletion-banner';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isScheduledForDeletion() ?? false;
    }

    public function getRemainingDays(): int
    {
        /** @var User $user */
        $user = auth()->user();

        return max(0, 30 - (int) $user->scheduled_for_deletion_at->diffInDays(now()));
    }

    public function cancelDeletionAction(): Action
    {
        return Action::make('cancelDeletion')
            ->label('Cancel deletion')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('primary')
            ->action(function (): void {
                /** @var User $user */
                $user = auth()->user();

                $user->update(['scheduled_for_deletion_at' => null]);

                Notification::make()
                    ->success()
                    ->title('Account deletion cancelled.')
                    ->send();
            });
    }
}
