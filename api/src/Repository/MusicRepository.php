<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\Music;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Music>
 */
class MusicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Music::class);
    }

    public function findMostLiked(int $limit = 10): array
    {
        return $this->createQueryBuilder('music')
            ->innerJoin(Favorite::class, 'favorite', 'WITH', 'favorite.music = music')
            ->groupBy('music.id')
            ->orderBy('COUNT(favorite.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
