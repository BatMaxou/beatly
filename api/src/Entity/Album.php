<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: 'api_create_album',
            normalizationContext: ['groups' => ['album:read']],
            denormalizationContext: ['groups' => ['album:write']],
        ),
        new Patch(
            name: 'api_update_album',
            normalizationContext: ['groups' => ['album:read']],
            denormalizationContext: ['groups' => ['album:update']],
        ),
        new Get(
            name: 'api_get_album',
            normalizationContext: ['groups' => ['album:read']],
        ),
        new GetCollection(
            name: 'api_get_album_collection',
            normalizationContext: ['groups' => ['album:collection:read']],
        ),
        new Delete(
            name: 'api_delete_album',
        ),
    ]
)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wallpaper = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $releaseDate = null;

    /**
     * @var Collection<int, Music>
     */
    #[ORM\ManyToMany(targetEntity: Music::class, mappedBy: 'albums')]
    private Collection $musics;

    public function __construct()
    {
        $this->musics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getWallpaper(): ?string
    {
        return $this->wallpaper;
    }

    public function setWallpaper(?string $wallpaper): static
    {
        $this->wallpaper = $wallpaper;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
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
            $music->addAlbum($this);
        }

        return $this;
    }

    public function removeMusic(Music $music): static
    {
        if ($this->musics->removeElement($music)) {
            $music->removeAlbum($this);
        }

        return $this;
    }
}
