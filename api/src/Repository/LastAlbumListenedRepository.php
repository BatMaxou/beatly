<?php

namespace App\Repository;

use App\Entity\LastAlbumListened;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastAlbumListened>
 */
class LastAlbumListenedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastAlbumListened::class);
    }
}
