<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CustomDashboard extends Dashboard
{
    protected static ?string $slug = '/';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = -1;

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
        return [];
    }

    public function getWidgets(): array
    {
        return [];
    }
}
