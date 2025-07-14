<?php

namespace App\Domain\Command;

use App\Entity\Album;
use App\Entity\Music;
use App\Entity\Playlist;

class AddToQueueCommand
{
    /**
     * @param Music[] $musics
     */
    public function __construct(
        public readonly bool $shouldBeNext = false,
        public readonly ?Music $music = null,
        public readonly ?Playlist $playlist = null,
        public readonly ?Album $album = null,
        public readonly ?array $musics = null,
        public readonly ?int $currentPosition = null,
    ) {
    }
}
