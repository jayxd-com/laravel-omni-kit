<?php

namespace App\Enums;

enum TeamRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Team Owner',
            self::ADMIN => 'Team Admin',
            self::MEMBER => 'Team Member',
        };
    }
}
