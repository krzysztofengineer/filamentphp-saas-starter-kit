<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PendingDeletionBanner;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected static ?string $slug = '/';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = -1;

    protected string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    public function getHeaderWidgets(): array
    {
        return [
            PendingDeletionBanner::class,
        ];
    }
}
