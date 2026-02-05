<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Favorite;
use App\Entity\FavoriteAlbum;
use App\Entity\FavoriteMusic;
use App\Entity\FavoritePlaylist;
use App\Entity\Interface\LikableEntityInterface;
use App\Entity\Music;
use App\Entity\PlatformPlaylist;
use App\Entity\Playlist;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public const MAPPING = [
        Music::class => FavoriteMusic::class,
        Album::class => FavoriteAlbum::class,
        Playlist::class => FavoritePlaylist::class,
        PlatformPlaylist::class => FavoritePlaylist::class,
    ];

    public const ATTR_MAPPING = [
        Music::class => 'music',
        Album::class => 'album',
        Playlist::class => 'playlist',
        PlatformPlaylist::class => 'playlist',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findFor(LikableEntityInterface $liked, User $user): ?Favorite
    {
        $em = $this->getEntityManager();
        // Handle Proxy given by Normalizer
        $realClass = $em->getClassMetadata($liked::class)->getName();
        $class = self::MAPPING[$realClass];

        return $em->createQueryBuilder()
            ->select('liked')
            ->from($class, 'liked')
            ->where('liked.user = :user')
            ->andWhere(\sprintf('liked.%s = :liked', self::ATTR_MAPPING[$realClass]))
            ->setParameter('user', $user)
            ->setParameter('liked', $liked)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
