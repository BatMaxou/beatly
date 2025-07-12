<?php

namespace App\Enum;

enum RoleEnum: string
{
    case PLATFORM = 'ROLE_PLATFORM';
    case ARTIST = 'ROLE_ARTIST';
    case USER = 'ROLE_USER';
    case BANNED = 'ROLE_BANNED';
}
