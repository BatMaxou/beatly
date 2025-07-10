<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Favorite;
use App\Entity\Music;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findMostPopular(int $limit = 10): array
    {
        return $this->createQueryBuilder('category')
            ->innerJoin(Music::class, 'music', 'WITH', 'category MEMBER OF music.categories')
            ->innerJoin(Favorite::class, 'favorite', 'WITH', 'favorite.music = music')
            ->groupBy('category.id')
            ->orderBy('COUNT(favorite.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
