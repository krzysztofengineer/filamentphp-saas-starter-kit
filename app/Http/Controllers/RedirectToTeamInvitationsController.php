<?php

namespace App\Http\Controllers;

use App\Filament\Clusters\AccountSettings\Pages\TeamInvitations;
use Illuminate\Http\RedirectResponse;

class RedirectToTeamInvitationsController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect(TeamInvitations::getUrl(['tenant' => auth()->user()->currentTeam]));
    }
}
