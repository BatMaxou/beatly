<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\RoleEnum;
use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            name: 'api_get_artist',
            normalizationContext: ['groups' => ['artist:read']],
        ),
        new GetCollection(
            name: 'api_get_artist_collection',
            normalizationContext: ['groups' => ['artist:collection:read']],
        ),
    ]
)]
class Artist extends User
{
  /**
   * @var Collection<int, Music>
   */
  #[ORM\ManyToMany(targetEntity: Music::class, mappedBy: 'artists')]
  private Collection $musics;

  /**
   * @var Collection<int, Album>
   */
  #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'artist', orphanRemoval: true)]
  private Collection $albums;

  public function __construct()
  {
    parent::__construct();
    $this->addRole(RoleEnum::ARTIST);
    $this->musics = new ArrayCollection();
    $this->albums = new ArrayCollection();
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

  /**
   * @return Collection<int, Album>
   */
  public function getAlbums(): Collection
  {
      return $this->albums;
  }

  public function addAlbum(Album $album): static
  {
      if (!$this->albums->contains($album)) {
          $this->albums->add($album);
          $album->setArtist($this);
      }

      return $this;
  }

  public function removeAlbum(Album $album): static
  {
      if ($this->albums->removeElement($album)) {
          // set the owning side to null (unless already changed)
          if ($album->getArtist() === $this) {
              $album->setArtist(null);
          }
      }

      return $this;
  }
}
