<?php

namespace App\Entity;

use App\Entity\Interface\ListenableEntityInterface;
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

    public function getTarget(): ?Music
    {
        return $this->music;
    }

    /**
     * @param Music|null $music
     */
    public function setTarget(?ListenableEntityInterface $music): static
    {
        $this->music = $music;

        return $this;
    }
}
