<?php

namespace App\Repository;

use App\Entity\FavoritePlaylist;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoritePlaylist>
 */
class FavoritePlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoritePlaylist::class);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['addedAt' => 'DESC']);
    }
}
