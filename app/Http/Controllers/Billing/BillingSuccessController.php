<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;

class BillingSuccessController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        Notification::make()
            ->title('Thanks for subscribing!')
            ->body('Payment received. Your plan will update in a few seconds.')
            ->success()
            ->send();

        return redirect('/app');
    }
}
