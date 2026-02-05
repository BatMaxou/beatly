<?php

namespace App\Entity;

use App\Entity\Interface\ListenableEntityInterface;
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

    public function getTarget(): ?Playlist
    {
        return $this->playlist;
    }

    /**
     * @param Playlist|null $playlist
     */
    public function setTarget(?ListenableEntityInterface $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }
}
