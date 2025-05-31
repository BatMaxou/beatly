<?php

namespace App\Entity;

use App\Enum\RoleEnum;
use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist extends User
{
  /**
   * @var Collection<int, Music>
   */
  #[ORM\ManyToMany(targetEntity: Music::class, mappedBy: 'artists')]
  private Collection $musics;

  public function __construct()
  {
    parent::__construct();
    $this->addRole(RoleEnum::ARTIST);
    $this->musics = new ArrayCollection();
  }

  /**
   * @return Collection<int, Music>
   */
  public function getMusics(): Collection
  {
      return $this->musics;
  }

  public function addMusic(Music $music): static
  {
      if (!$this->musics->contains($music)) {
          $this->musics->add($music);
          $music->addArtist($this);
      }

      return $this;
  }

  public function removeMusic(Music $music): static
  {
      if ($this->musics->removeElement($music)) {
          $music->removeArtist($this);
      }

      return $this;
  }
}
