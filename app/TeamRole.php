<?php

namespace App;

enum TeamRole: string
{
    case Owner = 'owner';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Member => 'Member',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Owner => 'primary',
            self::Member => 'gray',
        };
    }
}
