<?php

namespace App;

enum TeamRole: string
{
    case Administrator = 'administrator';
    case Manager = 'manager';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Manager => 'Manager',
            self::Member => 'Member',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Administrator => 'primary',
            self::Manager => 'warning',
            self::Member => 'gray',
        };
    }

    public function canManageTeam(): bool
    {
        return $this === self::Administrator || $this === self::Manager;
    }

    public function canDeleteTeam(): bool
    {
        return $this === self::Administrator;
    }
}
