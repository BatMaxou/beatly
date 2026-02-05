<?php

namespace App\Repository;

use App\Entity\FavoritePlaylist;
use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function findMostLiked(int $limit = 10): array
    {
        return $this->createQueryBuilder('playlist')
            ->innerJoin(FavoritePlaylist::class, 'favorite', 'WITH', 'favorite.playlist = playlist')
            ->groupBy('playlist.id')
            ->orderBy('COUNT(favorite.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
