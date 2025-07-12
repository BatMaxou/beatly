<?php

namespace App\Entity;

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

    public function getTarget(): ?object
    {
        return $this->album;
    }

    public function setTarget(?object $album): static
    {
        $this->album = $album;

        return $this;
    }
}
