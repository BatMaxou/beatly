<?php

namespace App\Domain\Command;

use App\Entity\Album;
use App\Entity\Music;
use App\Entity\Playlist;

class AddFavoriteCommand
{
    public function __construct(
        public readonly ?Music $music = null,
        public readonly ?Playlist $playlist = null,
        public readonly ?Album $album = null,
    ) {
    }
}
