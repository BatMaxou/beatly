<?php

namespace App\Enum;

enum EmailTypeEnum: string
{
    case FORGOT_PASSWORD = 'forgot-password';
    case ACCEPT_ARTIST_REQUEST = 'accept-artist-request';
    case DECLINE_ARTIST_REQUEST = 'decline-artist-request';
}
