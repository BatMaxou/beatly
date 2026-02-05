<?php

namespace App\Entity;

use App\Entity\Interface\ListenableEntityInterface;
use App\Repository\LastAlbumListenedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends LastListened<Album>
 */
#[ORM\Entity(repositoryClass: LastAlbumListenedRepository::class)]
class LastAlbumListened extends LastListened
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    public function getTarget(): ?Album
    {
        return $this->album;
    }

    /**
     * @param Album|null $album
     */
    public function setTarget(?ListenableEntityInterface $album): static
    {
        $this->album = $album;

        return $this;
    }
}
