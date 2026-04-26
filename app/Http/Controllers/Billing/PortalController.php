<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return $request->user()->redirectToBillingPortal(url('/app'));
    }
}
