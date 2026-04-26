<?php

use Filament\Facades\Filament;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: [
            'stripe/*',
        ]);

        $middleware->redirectGuestsTo(fn (): string => route('filament.app.auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $panel = Filament::getPanel('app');

            if (! $request->is($panel->getPath().'/*') && ! $request->is($panel->getPath())) {
                return null;
            }

            if ($request->expectsJson() || $request->is('livewire/*')) {
                return null;
            }

            return redirect($panel->getUrl());
        });
    })->create();
