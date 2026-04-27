<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;

class BillingCancelController extends Controller
{
    public function __invoke(Team $team): RedirectResponse
    {
        Notification::make()
            ->title('Payment was not completed')
            ->body('Nothing was charged. You can try again anytime.')
            ->warning()
            ->send();

        return redirect(Filament::getUrl(tenant: $team));
    }
}
