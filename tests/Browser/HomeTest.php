<?php

it('renders the marketing landing page', function () {
    visit('/')
        ->assertNoSmoke();
});

it('shows the login and register links in the topbar', function () {
    visit('/')
        ->assertPresent('[data-testid="topbar-login"]')
        ->assertPresent('[data-testid="topbar-register"]');
});

it('renders the privacy page', function () {
    visit('/privacy')
        ->assertNoSmoke();
});

it('renders the terms page', function () {
    visit('/terms')
        ->assertNoSmoke();
});
