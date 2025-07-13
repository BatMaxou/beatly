<?php

namespace App\Repository;

use App\Entity\Queue;
use App\Entity\QueueItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QueueItem>
 */
class QueueItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueueItem::class);
    }

    public function findAllByQueueAfterPosition(Queue $queue, int $position): array
    {
        return $this->createQueryBuilder('item')
            ->andWhere('item.queue = :queue')
            ->andWhere('item.position > :position')
            ->setParameter('queue', $queue)
            ->setParameter('position', $position)
            ->orderBy('item.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
