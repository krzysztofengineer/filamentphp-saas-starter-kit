<?php

namespace App\Http\Middleware;

use App\Actions\Teams\SaveUserCurrentTeam;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SaveCurrentTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $tenant = Filament::getTenant();

        if ($tenant === null) {
            return $next($request);
        }

        (new SaveUserCurrentTeam)->handle(Auth::user(), $tenant);

        return $next($request);
    }
}
