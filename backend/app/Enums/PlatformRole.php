<?php

namespace App\Enums;

enum PlatformRole: string
{
    case SUPER_ADMIN = 'platform_superadmin';

    case ADMIN = 'platform_admin';
    case MANAGER = 'platform_manager';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Platform Super Admin',
            self::ADMIN => 'Platform Admin',
            self::MANAGER => 'Platform Manager',
        };
    }
}
