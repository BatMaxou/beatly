<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\LastAlbumListened;
use App\Entity\LastListened;
use App\Entity\LastMusicListened;
use App\Entity\LastPlaylistListened;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastListened>
 */
class LastListenedRepository extends ServiceEntityRepository
{
    public const MAPPING = [
        Music::class => LastMusicListened::class,
        Album::class => LastAlbumListened::class,
        Playlist::class => LastPlaylistListened::class,
    ];

    public const ATTR_MAPPING = [
        Music::class => 'music',
        Album::class => 'album',
        Playlist::class => 'playlist',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastListened::class);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['listenedAt' => 'DESC']);
    }

    public function deleteDuplicates(ListenableEntityInterface $listened, User $user): void
    {
        $em = $this->getEntityManager();
        $class = self::MAPPING[$listened::class];

        $duplicates = $em->createQueryBuilder()
            ->select('listened')
            ->from($class, 'listened')
            ->where('listened.user = :user')
            ->andWhere(\sprintf('listened.%s = :listened', self::ATTR_MAPPING[$listened::class]))
            ->setParameter('user', $user)
            ->setParameter('listened', $listened)
            ->getQuery()
            ->getResult();

        foreach ($duplicates as $duplicate) {
            $em->remove($duplicate);
        }

        $em->flush();
    }

    public function deleteOutdated(User $user): void
    {
        $lastListened = $this->findByUser($user);

        if (count($lastListened) <= 10) {
            return;
        }

        $toDelete = array_slice($lastListened, 10);

        $em = $this->getEntityManager();
        foreach ($toDelete as $last) {
            $em->remove($last);
        }

        $em->flush();
    }

    public function create(ListenableEntityInterface $listened, User $user): void
    {
        $class = self::MAPPING[$listened::class];
        $lastListened = new $class();
        $lastListened->setUser($user);
        $lastListened->setTarget($listened);

        $em = $this->getEntityManager();
        $em->persist($lastListened);
        $em->flush();
    }
}
