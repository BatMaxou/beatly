<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Interface\EmbeddableEntityInterface;
use App\Enum\EmbeddingEnum;
use App\Enum\RoleEnum;
use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

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
class Artist extends User implements EmbeddableEntityInterface
{
    /**
     * @var Collection<int, Music>
     */
    #[ORM\ManyToMany(targetEntity: Music::class, mappedBy: 'artists')]
    private Collection $featuredMusics;

    /**
     * @var Collection<int, Album>
     */
    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'artist', orphanRemoval: true)]
    private Collection $albums;

    /**
     * @var Collection<int, Music>
     */
    #[ORM\OneToMany(targetEntity: Music::class, mappedBy: 'mainArtist', orphanRemoval: true)]
    private Collection $musics;

    #[ORM\Column]
    private string $uuid;

    public function __construct()
    {
        parent::__construct();
        $this->addRole(RoleEnum::ARTIST);
        $this->featuredMusics = new ArrayCollection();
        $this->albums = new ArrayCollection();
        $this->musics = new ArrayCollection();
        $this->uuid = Uuid::v4();
    }

    public static function getClassIdentifier(): string
    {
        return 'artist';
    }

    /**
     * @return Collection<int, Music>
     */
    public function getFeaturedMusics(): Collection
    {
        return $this->featuredMusics;
    }

    public function addFeaturedMusic(Music $music): static
    {
        if (!$this->featuredMusics->contains($music)) {
            $this->featuredMusics->add($music);
            $music->addArtist($this);
        }

        return $this;
    }

    public function removeFeaturedMusic(Music $music): static
    {
        if ($this->featuredMusics->removeElement($music)) {
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
            $music->setMainArtist($this);
        }

        return $this;
    }

    public function removeMusic(Music $music): static
    {
        if ($this->musics->removeElement($music)) {
            // set the owning side to null (unless already changed)
            if ($music->getMainArtist() === $this) {
                $music->setMainArtist(null);
            }
        }

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function prepareForEmbedding(EmbeddingEnum $type): string
    {
        return match ($type) {
            EmbeddingEnum::SEARCH => \sprintf('%s - %s', $this->getClassIdentifier(), $this->getName() ?? ''),
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
