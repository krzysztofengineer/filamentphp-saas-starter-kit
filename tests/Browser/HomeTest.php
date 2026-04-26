<?php

it('renders the marketing landing page with all sections', function () {
    visit('/')
        ->assertSee('Build your SaaS faster')
        ->assertPresent('[data-testid="landing-hero"]')
        ->assertPresent('[data-testid="landing-features"]')
        ->assertPresent('[data-testid="landing-how-it-works"]')
        ->assertPresent('[data-testid="landing-pricing"]')
        ->assertPresent('[data-testid="landing-faq"]')
        ->assertPresent('[data-testid="landing-footer"]')
        ->assertSee('Free')
        ->assertSee('Starter')
        ->assertSee('Pro')
        ->assertNoJavaScriptErrors();
});

it('shows the login and register links in the topbar', function () {
    visit('/')
        ->assertPresent('[data-testid="topbar-login"]')
        ->assertPresent('[data-testid="topbar-register"]');
});

it('renders the privacy page', function () {
    visit('/privacy')->assertSee('Privacy Policy')->assertNoJavaScriptErrors();
});

it('renders the terms page', function () {
    visit('/terms')->assertSee('Terms of Service')->assertNoJavaScriptErrors();
});
