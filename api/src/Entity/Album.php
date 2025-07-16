<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\AlbumCreationProcessor;
use App\Api\Processor\AlbumFilesProcessor;
use App\Entity\Interface\EmbeddableEntityInterface;
use App\Entity\Interface\LikableEntityInterface;
use App\Entity\Interface\ListenableEntityInterface;
use App\Enum\ApiReusableRoute;
use App\Enum\EmbeddingEnum;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: ApiReusableRoute::CREATE_ALBUM->value,
            processor: AlbumCreationProcessor::class,
            normalizationContext: ['groups' => ['album:read']],
            denormalizationContext: ['groups' => ['album:write']],
        ),
        new Post(
            name: ApiReusableRoute::UPDATE_ALBUM_FILES->value,
            uriTemplate: '/albums/{id}/files',
            processor: AlbumFilesProcessor::class,
            normalizationContext: ['groups' => ['album:read']],
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
class Album implements ListenableEntityInterface, EmbeddableEntityInterface, LikableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverName = null;

    #[Vich\UploadableField(mapping: 'album_cover', fileNameProperty: 'coverName')]
    private ?File $cover = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wallpaperName = null;

    #[Vich\UploadableField(mapping: 'album_wallpaper', fileNameProperty: 'wallpaperName')]
    private ?File $wallpaper = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Music>
     */
    #[ORM\OneToMany(targetEntity: Music::class, mappedBy: 'album', cascade: ['persist'])]
    private Collection $musics;

    #[ORM\ManyToOne(inversedBy: 'albums')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;

    #[ORM\Column]
    private string $uuid;

    public function __construct()
    {
        $this->musics = new ArrayCollection();
        $this->uuid = Uuid::v4();
    }

    public static function getClassIdentifier(): string
    {
        return 'album';
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

    public function getCoverName(): ?string
    {
        return $this->coverName;
    }

    public function setCoverName(?string $coverName): static
    {
        $this->coverName = $coverName;

        return $this;
    }

    public function getCover(): ?File
    {
        return $this->cover;
    }

    public function setCover(?File $cover = null): static
    {
        $this->cover = $cover;

        if ($cover) {
            // Important to update at least one field to trigger the doctrine events
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getWallpaperName(): ?string
    {
        return $this->wallpaperName;
    }

    public function setWallpaperName(?string $wallpaperName): static
    {
        $this->wallpaperName = $wallpaperName;

        return $this;
    }

    public function getWallpaper(): ?File
    {
        return $this->wallpaper;
    }

    public function setWallpaper(?File $wallpaper = null): static
    {
        $this->wallpaper = $wallpaper;

        if ($wallpaper) {
            // Important to update at least one field to trigger the doctrine events
            $this->updatedAt = new \DateTimeImmutable();
        }

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
            $music->setAlbum($this);
        }

        return $this;
    }

    public function removeMusic(Music $music): static
    {
        if ($this->musics->removeElement($music)) {
            // set the owning side to null (unless already changed)
            if ($music->getAlbum() === $this) {
                $music->setAlbum(null);
            }
        }

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function prepareForEmbedding(EmbeddingEnum $type): string
    {
        return match ($type) {
            EmbeddingEnum::SEARCH => \sprintf('%s - %s - %s', $this->getClassIdentifier(), $this->getTitle(), $this->getArtist()?->getName() ?? ''),
            default => throw new \InvalidArgumentException(sprintf('Unsupported embedding type: %s', $type->value)),
        };
    }

    public function supportEmbedding(): array
    {
        return [
            EmbeddingEnum::SEARCH,
        ];
    }
}
