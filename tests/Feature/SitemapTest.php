<?php

use function Pest\Laravel\get;

it('serves an XML sitemap with the public landing pages', function () {
    $response = get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
    $response->assertSee(url('/privacy'), false);
    $response->assertSee(url('/terms'), false);
});
