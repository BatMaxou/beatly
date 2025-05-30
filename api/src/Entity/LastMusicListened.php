<?php

namespace App\Entity;

use App\Repository\LastMusicListenedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LastMusicListenedRepository::class)]
class LastMusicListened extends LastListened
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Music $music = null;

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function setMusic(?Music $music): static
    {
        $this->music = $music;

        return $this;
    }
}
