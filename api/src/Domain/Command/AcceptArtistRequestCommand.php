<?php

namespace App\Domain\Command;

use App\Entity\ArtistRequest;

class AcceptArtistRequestCommand
{
    public function __construct(
        public ArtistRequest $artistRequest,
    ) {
    }
}
