<?php

namespace App\Entity;

use App\Repository\QueueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QueueRepository::class)]
class Queue extends QueueBase
{
    #[ORM\OneToOne(inversedBy: 'queue')]
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
