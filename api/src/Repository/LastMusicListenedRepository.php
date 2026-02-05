<?php

namespace App\Repository;

use App\Entity\LastMusicListened;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastMusicListened>
 */
class LastMusicListenedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastMusicListened::class);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['listenedAt' => 'DESC']);
    }
}
