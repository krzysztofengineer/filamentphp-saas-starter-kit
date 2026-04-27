<?php

use App\Http\Controllers\Billing\BillingCancelController;
use App\Http\Controllers\Billing\BillingSuccessController;
use App\Http\Controllers\Billing\CheckoutController;
use App\Http\Controllers\Billing\PortalController;
use App\Http\Controllers\Pwa\ManifestController;
use App\Http\Controllers\Pwa\PushSubscriptionController;
use App\Http\Controllers\Pwa\ServiceWorkerController;
use App\Http\Controllers\Seo\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sw.js', ServiceWorkerController::class)->name('pwa.sw');
Route::get('/manifest.webmanifest', ManifestController::class)->name('pwa.manifest');
Route::view('/offline', 'pwa.offline')->name('pwa.offline');

Route::get('/sitemap.xml', SitemapController::class)->name('seo.sitemap');

Route::middleware('auth')->prefix('push')->name('push.')->group(function () {
    Route::post('/subscribe', [PushSubscriptionController::class, 'store'])->name('subscribe');
    Route::delete('/subscribe', [PushSubscriptionController::class, 'destroy'])->name('unsubscribe');
});

Route::middleware('auth')->prefix('billing/{team}')->name('billing.')->group(function () {
    Route::get('/checkout/{plan}/{interval}', CheckoutController::class)->name('checkout');
    Route::get('/portal', PortalController::class)->name('portal');
    Route::get('/success', BillingSuccessController::class)->name('success');
    Route::get('/cancel', BillingCancelController::class)->name('cancel');
});
