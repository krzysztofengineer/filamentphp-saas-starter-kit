<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Notifications\ResetPassword;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->bind(\Filament\Auth\Notifications\ResetPassword::class, ResetPassword::class);
    }

    public function boot(): void
    {
        //
    }
}
