<?php

namespace App\Filament\Home\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Privacy extends Page
{
    protected static ?string $slug = 'privacy';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.home.pages.privacy';

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
