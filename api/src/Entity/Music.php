<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\MusicFilesProcessor;
use App\Api\Processor\MusicUpsertProcessor;
use App\Entity\Interface\EmbeddableEntityInterface;
use App\Entity\Interface\LikableEntityInterface;
use App\Entity\Interface\ListenableEntityInterface;
use App\Enum\ApiReusableRoute;
use App\Enum\EmbeddingEnum;
use App\Repository\MusicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MusicRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: ApiReusableRoute::CREATE_MUSIC->value,
            processor: MusicUpsertProcessor::class,
            normalizationContext: ['groups' => ['music:read']],
            denormalizationContext: ['groups' => ['music:write']],
        ),
        new Post(
            name: ApiReusableRoute::UPDATE_MUSIC_FILES->value,
            uriTemplate: '/music/{id}/files',
            processor: MusicFilesProcessor::class,
            normalizationContext: ['groups' => ['music:read']],
        ),
        new Patch(
            name: ApiReusableRoute::UPDATE_MUSIC->value,
            processor: MusicUpsertProcessor::class,
            normalizationContext: ['groups' => ['music:read']],
            denormalizationContext: ['groups' => ['music:update']],
        ),
        new Get(
            name: 'api_get_music',
            normalizationContext: ['groups' => ['music:read']],
        ),
        new GetCollection(
            name: 'api_get_music_collection',
            normalizationContext: ['groups' => ['music:read']],
        ),
        new Delete(
            name: 'api_delete_music_file',
        ),
    ]
)]
class Music implements EmbeddableEntityInterface, ListenableEntityInterface, LikableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverName = null;

    #[Vich\UploadableField(mapping: 'music_cover', fileNameProperty: 'coverName')]
    private ?File $cover = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?MusicFile $file = null;

    #[ORM\Column]
    private ?int $listeningsNumber = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class)]
    private Collection $categories;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'featuredMusics')]
    private Collection $artists;

    /**
     * @var Collection<int, AlbumMusic>
     */
    #[ORM\OneToMany(targetEntity: AlbumMusic::class, mappedBy: 'music', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $albums;

    #[ORM\ManyToOne(inversedBy: 'musics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $mainArtist = null;

    #[ORM\Column]
    private string $uuid;

    #[ORM\Column]
    private ?int $duration = null;

    public function __construct()
    {
        $this->listeningsNumber = 0;
        $this->categories = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->albums = new ArrayCollection();
        $this->uuid = Uuid::v4();
    }

    public static function getClassIdentifier(): string
    {
        return 'music';
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

    public function getFile(): ?MusicFile
    {
        return $this->file;
    }

    public function setFile(MusicFile $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getListeningsNumber(): ?int
    {
        return $this->listeningsNumber;
    }

    public function setListeningsNumber(int $listeningsNumber): static
    {
        $this->listeningsNumber = $listeningsNumber;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): static
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
        }

        return $this;
    }

    public function removeArtist(Artist $artist): static
    {
        $this->artists->removeElement($artist);

        return $this;
    }

    /**
     * @return Collection<int, AlbumMusic>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(AlbumMusic $album): static
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->setMusic($this);
        }

        return $this;
    }

    public function removeAlbum(AlbumMusic $album): static
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getAlbum() === $this) {
                $album->setAlbum(null);
            }
        }

        return $this;
    }

    public function getMainArtist(): ?Artist
    {
        return $this->mainArtist;
    }

    public function setMainArtist(?Artist $mainArtist): static
    {
        $this->mainArtist = $mainArtist;

        return $this;
    }

    public function listen(): void
    {
        ++$this->listeningsNumber;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function prepareForEmbedding(EmbeddingEnum $type): string
    {
        return match ($type) {
            EmbeddingEnum::RECOMMENDATION => $this->prepareForRecommendationEmbedding(),
            EmbeddingEnum::SEARCH => $this->prepareForSearchEmbedding(),
            default => throw new \InvalidArgumentException(sprintf('Unsupported embedding type: %s', $type->value)),
        };
    }

    public function supportEmbedding(): array
    {
        return [
            EmbeddingEnum::RECOMMENDATION,
            EmbeddingEnum::SEARCH,
        ];
    }

    private function prepareForRecommendationEmbedding(): string
    {
        $categories = $this->categories->map(fn (Category $category) => md5($category->getName()))->toArray();
        $artists = $this->artists->map(fn (Artist $artist) => md5($artist->getName()))->toArray();
        $albums = $this->albums->map(fn (AlbumMusic $album) => md5($album->getAlbum()->getTitle()))->toArray();

        return sprintf(
            '%s - %s - %s - %s',
            implode(' ', $categories),
            md5($this->getMainArtist()?->getName() ?? ''),
            implode(' ', $artists),
            implode(' ', $albums)
        );
    }

    private function prepareForSearchEmbedding(): string
    {
        return sprintf(
            '%s - %2$s %2$s %2$s - %s - %s - %s',
            $this->getClassIdentifier(),
            $this->title,
            implode(' ', $this->categories->map(fn (Category $category) => $category->getName())->toArray()),
            $this->getMainArtist()?->getName() ?? '',
            implode(' ', $this->artists->map(fn (Artist $artist) => $artist->getName())->toArray())
        );
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
}
