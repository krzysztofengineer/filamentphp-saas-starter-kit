<?php

namespace App\Http\Controllers\Billing;

use App\BillingInterval;
use App\BillingPlan;
use App\Http\Controllers\Controller;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __invoke(Request $request, BillingPlan $plan, BillingInterval $interval): RedirectResponse|Responsable
    {
        $priceId = $plan->priceId($interval);

        if (blank($priceId)) {
            Notification::make()
                ->danger()
                ->title('Stripe is not configured')
                ->body("Set STRIPE_PRICE_{$plan->name}_{$interval->name} in your .env to enable checkout for the {$plan->label()} {$interval->label()} plan.")
                ->persistent()
                ->send();

            return redirect()->back();
        }

        return $request->user()
            ->newSubscription('default', $priceId)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.cancel'),
            ]);
    }
}
