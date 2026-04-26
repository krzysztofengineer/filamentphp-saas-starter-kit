<?php

namespace App\Http\Middleware;

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

        if (Filament::getTenant() === null) {
            return $next($request);
        }

        if (Auth::user()->current_team_id !== Filament::getTenant()->id) {
            Auth::user()->update([
                'current_team_id' => Filament::getTenant()->id,
            ]);
            Auth::user()->unsetRelation('currentTeam');
        }

        return $next($request);
    }
}
