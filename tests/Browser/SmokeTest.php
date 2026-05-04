<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('has no smoke', function () {
    visit('/')->assertNoJavaScriptErrors();
    visit('/privacy')->assertNoJavaScriptErrors();
    visit('/terms')->assertNoJavaScriptErrors();
    visit('/app/login')->assertNoJavaScriptErrors();
    visit('/app/register')->assertNoJavaScriptErrors();

    $user = User::factory()->create();
    $tenant = $user->currentTeam;
    actingAs($user);

    visit('/app/'.$tenant->uuid)->assertNoJavaScriptErrors();
    visit('/app/'.$tenant->uuid.'/account/settings')->assertNoJavaScriptErrors();
    visit('/app/'.$tenant->uuid.'/account/team-invitations')->assertNoJavaScriptErrors();
    visit('/app/'.$tenant->uuid.'/account/advanced')->assertNoJavaScriptErrors();
    visit('/app/'.$tenant->uuid.'/settings/profile')->assertNoJavaScriptErrors();
    visit('/app/'.$tenant->uuid.'/settings/members')->assertNoJavaScriptErrors();
    visit('/app/'.$tenant->uuid.'/settings/subscription')->assertNoJavaScriptErrors();
    visit('/app/new')->assertNoJavaScriptErrors();
    visit('/app/password-reset/request')->assertNoJavaScriptErrors();
});
