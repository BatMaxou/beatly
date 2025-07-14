<?php

namespace App\Entity;

use App\Repository\QueueItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueItemRepository::class)]
class QueueItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Music $music = null;

    #[ORM\ManyToOne(inversedBy: 'queueItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QueueBase $queue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function setMusic(?Music $music): static
    {
        $this->music = $music;

        return $this;
    }

    public function getQueue(): ?QueueBase
    {
        return $this->queue;
    }

    public function setQueue(?QueueBase $queue): static
    {
        $this->queue = $queue;

        return $this;
    }
}
