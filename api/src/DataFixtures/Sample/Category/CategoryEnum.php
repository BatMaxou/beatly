<?php

namespace App\DataFixtures\Sample\Category;

enum CategoryEnum: string
{
    case POP = 'Pop';
    case HIP_HOP = 'Hip-Hop';
    case ROCK = 'Rock';
    case ELECTRONIC = 'Electronic';
    case R_B = 'R&B';
    case COUNTRY = 'Country';
    case JAZZ = 'Jazz';
    case CLASSICAL = 'Classical';
    case REGGAE = 'Reggae';
    case ALTERNATIVE = 'Alternative';
    case INDIE = 'Indie';
    case FOLK = 'Folk';
    case FUNK = 'Funk';
    case SOUL = 'Soul';
    case BLUES = 'Blues';
    case TRAP = 'Trap';
    case HOUSE = 'House';
    case TECHNO = 'Techno';
}
