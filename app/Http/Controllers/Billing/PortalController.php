<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function __invoke(Request $request, Team $team): RedirectResponse
    {
        if (! $team->canBeManagedBy($request->user())) {
            throw new AuthorizationException;
        }

        return $team->redirectToBillingPortal(Filament::getUrl(tenant: $team));
    }
}
