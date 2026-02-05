<?php

namespace App\Repository;

use App\Entity\LastPlaylistListened;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastPlaylistListened>
 */
class LastPlaylistListenedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastPlaylistListened::class);
    }
}
