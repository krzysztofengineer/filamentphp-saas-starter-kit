<?php

namespace App\Http\Middleware;

use App\Filament\Account\PendingDeletion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectPendingDeletion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user === null || ! $user->isDeleted()) {
            return $next($request);
        }

        if ($request->routeIs('filament.app.pages.pending-deletion', 'filament.app.auth.logout')) {
            return $next($request);
        }

        return redirect(PendingDeletion::getUrl());
    }
}
