<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('has no smoke', function () {
    visit('/')->assertNoJavaScriptErrors();
    visit('/privacy')->assertNoJavaScriptErrors();
    visit('/terms')->assertNoJavaScriptErrors();
    visit('/app/login')->assertNoJavaScriptErrors();
    visit('/app/register')->assertNoJavaScriptErrors();

    $user = User::factory()->withTeam()->create();
    actingAs($user);

    visit('/app/'.$user->teams()->first()->uuid)->assertNoJavaScriptErrors();
    visit('/app/'.$user->teams()->first()->uuid.'/account/settings')->assertNoJavaScriptErrors();
    visit('/app/'.$user->teams()->first()->uuid.'/account/advanced')->assertNoJavaScriptErrors();
    visit('/app/'.$user->teams()->first()->uuid.'/settings/profile')->assertNoJavaScriptErrors();
    visit('/app/'.$user->teams()->first()->uuid.'/settings/members')->assertNoJavaScriptErrors();
    visit('/app/'.$user->teams()->first()->uuid.'/settings/subscription')->assertNoJavaScriptErrors();
    visit('/app/'.$user->teams()->first()->uuid.'/settings/advanced')->assertNoJavaScriptErrors();
    visit('/app/new')->assertNoJavaScriptErrors();
    visit('/app/password-reset/request')->assertNoJavaScriptErrors();
});
