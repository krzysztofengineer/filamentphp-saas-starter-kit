<?php

namespace App\Filament\AvatarProviders;

use App\Models\Team;
use Filament\AvatarProviders\Contracts;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeamLogoProvider implements Contracts\AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        if ($record instanceof Team && filled($record->logo)) {
            return Storage::disk('team-logos')->url($record->logo);
        }

        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $background = Color::convertToHex(FilamentColor::getColor('primary')[600] ?? Color::Gray[950]);

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&format=svg&color=FFFFFF&background='.urlencode($background);
    }
}
