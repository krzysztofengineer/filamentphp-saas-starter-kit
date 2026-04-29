<?php

namespace App;

enum TeamRole: string
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

    public function canManageTeam(): bool
    {
        return $this === self::Administrator;
    }

    public function canDeleteTeam(): bool
    {
        return $this === self::Administrator;
    }
}
