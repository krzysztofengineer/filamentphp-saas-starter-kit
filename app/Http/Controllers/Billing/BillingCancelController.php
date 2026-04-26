<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;

class BillingCancelController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        Notification::make()
            ->title('Payment was not completed')
            ->body('Nothing was charged to your card. You can try again anytime.')
            ->warning()
            ->send();

        return redirect('/app');
    }
}
