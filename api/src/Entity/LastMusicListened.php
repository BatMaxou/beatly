<?php

namespace App\Entity;

use App\Repository\LastMusicListenedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends LastListened<Music>
 */
#[ORM\Entity(repositoryClass: LastMusicListenedRepository::class)]
class LastMusicListened extends LastListened
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Music $music = null;

    public function getTarget(): ?object
    {
        return $this->music;
    }

    public function setTarget(?object $music): static
    {
        $this->music = $music;

        return $this;
    }
}
