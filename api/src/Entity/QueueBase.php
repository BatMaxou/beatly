<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Api\Provider\QueueProvider;
use App\Domain\Command\AddToQueueCommand;
use App\Domain\Command\ClearRandomQueueCommand;
use App\Domain\Command\GenerateRandomQueueCommand;
use App\Domain\Command\ResetQueueCommand;
use App\Enum\ApiReusableRoute;
use App\Repository\QueueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

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
        new Post(
            uriTemplate: '/queue/random/generate',
            name: 'api_generate_random_queue',
            messenger: 'input',
            input: GenerateRandomQueueCommand::class,
        ),
        new Post(
            uriTemplate: '/queue/random/clear',
            name: 'api_clear_random_queue',
            messenger: 'input',
            input: ClearRandomQueueCommand::class,
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
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'queue' => Queue::class,
    'random' => RandomQueue::class,
])]
abstract class QueueBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
