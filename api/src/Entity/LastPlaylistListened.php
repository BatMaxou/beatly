<?php

namespace App\Entity;

use App\Repository\LastPlaylistListenedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends LastListened<Playlist>
 */
#[ORM\Entity(repositoryClass: LastPlaylistListenedRepository::class)]
class LastPlaylistListened extends LastListened
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Playlist $playlist = null;

    public function getTarget(): ?object
    {
        return $this->playlist;
    }

    public function setTarget(?object $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }
}
