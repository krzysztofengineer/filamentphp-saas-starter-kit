<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum TeamRole: string implements HasLabel
{
    case Administrator = 'administrator';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Member => 'Member',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Administrator => 'primary',
            self::Member => 'gray',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Member => 'Member',
        };
    }

    public function canManageTeam(): bool
    {
        return $this === self::Administrator;
    }

    public function canDeleteTeam(): bool
    {
        return $this === self::Administrator;
    }
}
