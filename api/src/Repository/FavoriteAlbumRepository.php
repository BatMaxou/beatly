<?php

namespace App\Repository;

use App\Entity\FavoriteAlbum;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteAlbum>
 */
class FavoriteAlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteAlbum::class);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['addedAt' => 'DESC']);
    }
}
