<?php

use function Pest\Laravel\get;

it('serves the PWA manifest with the configured app name', function () {
    $response = get('/manifest.webmanifest');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/manifest+json');
    $response->assertJson([
        'name' => config('app.name'),
        'theme_color' => '#2563EB',
        'lang' => 'en',
    ]);
});
