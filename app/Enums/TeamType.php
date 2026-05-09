<?php

namespace App\Enums;

enum TeamType: string
{
    case Personal = 'personal';
    case Company = 'company';

    public function isPersonal(): bool
    {
        return $this === self::Personal;
    }
}
