<?php

namespace App\Domain\Command\Email;

use App\Entity\User;

class SendDeclineArtistRequestEmailCommand
{
    public function __construct(
        public User $user,
    ) {
    }
}
