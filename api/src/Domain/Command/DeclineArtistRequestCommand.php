<?php

namespace App\Domain\Command;

use App\Entity\ArtistRequest;

class DeclineArtistRequestCommand
{
    public function __construct(
        public ArtistRequest $artistRequest,
    ) {
    }
}
