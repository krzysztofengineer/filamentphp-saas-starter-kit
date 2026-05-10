<?php

use App\Http\Controllers\Billing\BillingCancelController;
use App\Http\Controllers\Billing\BillingSuccessController;
use App\Http\Controllers\Billing\CheckoutController;
use App\Http\Controllers\Billing\PortalController;
use App\Http\Controllers\Pwa\ManifestController;
use App\Http\Controllers\Pwa\PushSubscriptionController;
use App\Http\Controllers\Pwa\ServiceWorkerController;
use App\Http\Controllers\RedirectToTeamInvitationsController;
use App\Http\Controllers\Seo\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sw.js', ServiceWorkerController::class);
Route::get('/manifest.webmanifest', ManifestController::class)->name('pwa.manifest');
Route::view('/offline', 'pwa.offline');

Route::get('/sitemap.xml', SitemapController::class);

Route::middleware('auth')->group(function () {
    Route::get('/team-invitations', RedirectToTeamInvitationsController::class)->name('team-invitations.show');
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store']);
    Route::delete('/push/subscribe', [PushSubscriptionController::class, 'destroy']);
});

Route::middleware('auth')->prefix('billing/{team}')->name('billing.')->group(function () {
    Route::get('/checkout/{plan}/{interval}', CheckoutController::class)->name('checkout');
    Route::get('/portal', PortalController::class)->name('portal');
    Route::get('/success', BillingSuccessController::class)->name('success');
    Route::get('/cancel', BillingCancelController::class)->name('cancel');
});
