<?php

namespace App\Filament\Home\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Terms extends Page
{
    protected static ?string $slug = 'terms';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.home.pages.terms';

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
