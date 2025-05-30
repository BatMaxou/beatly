<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MusicRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
#[ApiResource]
class Music
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?MusicFile $file = null;

    #[ORM\Column]
    private ?int $listeningsNumber = null;

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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

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
}
