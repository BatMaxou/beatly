<?php

namespace App\Entity;

use App\Entity\Interface\LikableEntityInterface;
use App\Repository\FavoriteMusicRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends Favorite<Music>
 */
#[ORM\Entity(repositoryClass: FavoriteMusicRepository::class)]
class FavoriteMusic extends Favorite
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Music $music = null;

    public function getTarget(): ?Music
    {
        return $this->music;
    }

    /**
     * @param Music|null $music
     */
    public function setTarget(?LikableEntityInterface $music): static
    {
        $this->music = $music;

        return $this;
    }
}
