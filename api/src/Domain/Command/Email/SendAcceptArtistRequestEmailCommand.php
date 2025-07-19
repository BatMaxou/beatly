<?php

namespace App\Domain\Command\Email;

use App\Entity\Artist;

class SendAcceptArtistRequestEmailCommand
{
    public function __construct(
        public Artist $user,
    ) {
    }
}
