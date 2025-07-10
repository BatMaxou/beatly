<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\AlbumFilesProcessor;
use App\Entity\Interface\ListenableEntityInterface;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: 'api_create_album',
            normalizationContext: ['groups' => ['album:read']],
            denormalizationContext: ['groups' => ['album:write']],
        ),
        new Post(
            name: 'api_update_album_files',
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
class Album implements ListenableEntityInterface
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
     * @var Collection<int, AlbumMusic>
     */
    #[ORM\OneToMany(targetEntity: AlbumMusic::class, mappedBy: 'album', orphanRemoval: true, cascade: ['persist', 'remove'])]
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
     * @return Collection<int, AlbumMusic>
     */
    public function getMusics(): Collection
    {
        return $this->musics;
    }

    public function addMusic(AlbumMusic $music): static
    {
        if (!$this->musics->contains($music)) {
            $this->musics->add($music);
            $music->setAlbum($this);
        }

        return $this;
    }

    public function removeMusic(AlbumMusic $music): static
    {
        if ($this->musics->removeElement($music)) {
            // set the owning side to null (unless already changed)
            if ($music->getAlbum() === $this) {
                $music->setAlbum(null);
            }
        }

        return $this;
    }
}
