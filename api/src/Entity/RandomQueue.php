<?php

namespace App\Entity;

use App\Repository\RandomQueueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RandomQueueRepository::class)]
class RandomQueue extends QueueBase
{
    #[ORM\OneToOne(inversedBy: 'randomQueue')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
