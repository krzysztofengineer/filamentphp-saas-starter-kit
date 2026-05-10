<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Models\Team;
use App\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\LogoutResponse::class, LogoutResponse::class);
        $this->app->bind(\Filament\Auth\Notifications\ResetPassword::class, ResetPassword::class);
    }

    public function boot(): void
    {
        Cashier::useCustomerModel(Team::class);
    }
}
