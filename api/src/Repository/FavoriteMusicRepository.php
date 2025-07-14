<?php

namespace App\Repository;

use App\Entity\FavoriteMusic;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteMusic>
 */
class FavoriteMusicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteMusic::class);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['addedAt' => 'DESC']);
    }
}
