<?php

namespace App\Entity;

use App\Entity\Interface\LikableEntityInterface;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

/**
 * @template T of FavoriteMusic|FavoritePlaylist|FavoriteAlbum
 */
#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'music' => FavoriteMusic::class,
    'playlist' => FavoritePlaylist::class,
    'album' => FavoriteAlbum::class,
])]
abstract class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    /**
     * @return T|null
     */
    abstract public function getTarget(): ?LikableEntityInterface;

    /**
     * @param T $target
     */
    abstract public function setTarget(LikableEntityInterface $target): static;
}
