<?php

namespace App\Entity;

use App\Entity\Interface\ListenableEntityInterface;
use App\Repository\LastListenedRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

/**
 * @template T of LastMusicListened|LastPlaylistListened|LastAlbumListened
 */
#[ORM\Entity(repositoryClass: LastListenedRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'music' => LastMusicListened::class,
    'playlist' => LastPlaylistListened::class,
    'album' => LastAlbumListened::class,
])]
abstract class LastListened
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $listenedAt = null;

    public function __construct()
    {
        $this->listenedAt = new \DateTimeImmutable();
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

    public function getListenedAt(): ?\DateTimeImmutable
    {
        return $this->listenedAt;
    }

    /**
     * @return T|null
     */
    abstract public function getTarget(): ?ListenableEntityInterface;

    /**
     * @param T $target
     */
    abstract public function setTarget(ListenableEntityInterface $target): static;
}
