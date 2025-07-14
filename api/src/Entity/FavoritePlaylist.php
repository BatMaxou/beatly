<?php

namespace App\Entity;

use App\Entity\Interface\LikableEntityInterface;
use App\Repository\LastPlaylistListenedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends Favorite<Playlist>
 */
#[ORM\Entity(repositoryClass: LastPlaylistListenedRepository::class)]
class FavoritePlaylist extends Favorite
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
    public function setTarget(?LikableEntityInterface $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }
}
