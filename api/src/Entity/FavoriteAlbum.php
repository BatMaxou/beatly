<?php

namespace App\Entity;

use App\Entity\Interface\LikableEntityInterface;
use App\Repository\FavoriteAlbumRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends Favorite<Album>
 */
#[ORM\Entity(repositoryClass: FavoriteAlbumRepository::class)]
class FavoriteAlbum extends Favorite
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
    public function setTarget(?LikableEntityInterface $album): static
    {
        $this->album = $album;

        return $this;
    }
}
