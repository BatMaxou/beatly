<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\PlaylistCreationProcessor;
use App\Enum\ApiReusableRoute;
use App\Entity\Interface\ListenableEntityInterface;
use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'classic' => Playlist::class,
    'platform' => PlatformPlaylist::class,
])]
#[ApiResource(
    operations: [
        new Post(
            name: ApiReusableRoute::CREATE_PLAYLIST->value,
            processor: PlaylistCreationProcessor::class,
            normalizationContext: ['groups' => ['playlist:read']],
            denormalizationContext: ['groups' => ['playlist:write']],
        ),
        new Patch(
            name: 'api_update_playlist',
            normalizationContext: ['groups' => ['playlist:read']],
            denormalizationContext: ['groups' => ['playlist:update']],
        ),
        new Get(
            name: 'api_get_playlist',
            normalizationContext: ['groups' => ['playlist:read']],
        ),
        new GetCollection(
            name: 'api_get_playlist_collection',
            normalizationContext: ['groups' => ['playlist:collection:read']],
        ),
        new Delete(
            name: 'api_delete_playlist',
        ),
    ]
)]
class Playlist implements ListenableEntityInterface
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

    #[ORM\ManyToOne(inversedBy: 'playlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    /**
     * @var Collection<int, PlaylistMusic>
     */
    #[ORM\OneToMany(targetEntity: PlaylistMusic::class, mappedBy: 'playlist', orphanRemoval: true, cascade: ['persist', 'remove'])]
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

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection<int, PlaylistMusic>
     */
    public function getMusics(): Collection
    {
        return $this->musics;
    }

    public function addMusic(PlaylistMusic $music): static
    {
        if (!$this->musics->contains($music)) {
            $this->musics->add($music);
            $music->setPlaylist($this);
        }

        return $this;
    }

    public function removeMusic(PlaylistMusic $music): static
    {
        if ($this->musics->removeElement($music)) {
            // set the owning side to null (unless already changed)
            if ($music->getPlaylist() === $this) {
                $music->setPlaylist(null);
            }
        }

        return $this;
    }
}
