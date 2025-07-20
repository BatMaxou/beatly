<?php

namespace App\Enum;

enum VoterRoleEnum: string
{
    case ADMIN = 'VOTER_ROLE_ADMIN';
    case ARTIST = 'VOTER_ROLE_ARTIST';
    case RAW_USER = 'VOTER_ROLE_RAW_USER';
    case UNBANED = 'VOTER_ROLE_UNBANED';
    case OWNER = 'VOTER_ROLE_OWNER';
    case SELF = 'VOTER_ROLE_SELF';
}
