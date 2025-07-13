<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Api\Provider\QueueProvider;
use App\Domain\Command\AddToQueueCommand;
use App\Domain\Command\ResetQueueCommand;
use App\Enum\ApiReusableRoute;
use App\Repository\QueueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/queue/add',
            name: 'api_add_to_queue',
            messenger: 'input',
            input: AddToQueueCommand::class,
        ),
        new Post(
            uriTemplate: '/queue/reset',
            name: 'api_reset_queue',
            messenger: 'input',
            input: ResetQueueCommand::class,
        ),
        new Get(
            uriTemplate: '/queue',
            provider: QueueProvider::class,
            name: ApiReusableRoute::GET_QUEUE->value,
            normalizationContext: ['groups' => ['queue:read']]
        ),
    ]
)]
#[ORM\Entity(repositoryClass: QueueRepository::class)]
class Queue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'queue', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, QueueItem>
     */
    #[ORM\OneToMany(targetEntity: QueueItem::class, mappedBy: 'queue', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $queueItems;

    public function __construct()
    {
        $this->queueItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, QueueItem>
     */
    public function getQueueItems(): Collection
    {
        return $this->queueItems;
    }

    public function addQueueItem(QueueItem $queueItem): static
    {
        if (!$this->queueItems->contains($queueItem)) {
            $this->queueItems->add($queueItem);
            $queueItem->setQueue($this);
        }

        return $this;
    }

    public function removeQueueItem(QueueItem $queueItem): static
    {
        if ($this->queueItems->removeElement($queueItem)) {
            // set the owning side to null (unless already changed)
            if ($queueItem->getQueue() === $this) {
                $queueItem->setQueue(null);
            }
        }

        return $this;
    }

    public function clearQueueItems(): static
    {
        foreach ($this->queueItems as $item) {
            $this->removeQueueItem($item);
        }

        return $this;
    }

    public function getNextPosition(): int
    {
        if ($this->queueItems->isEmpty()) {
            return 1;
        }

        $lastItem = $this->queueItems->last();

        return $lastItem->getPosition() + 1;
    }
}
