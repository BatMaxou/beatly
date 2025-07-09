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
    const MAPPING = [
        Music::class => LastMusicListened::class,
        Album::class => LastAlbumListened::class,
        Playlist::class => LastPlaylistListened::class,
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastListened::class);
    }

    public function getListeningByDate(User $user, string $entityClass): array 
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('listened')
            ->from(self::MAPPING[$entityClass], 'listened')
            ->innerJoin(LastListened::class, 'abstract')
            ->where('listened.user = :user')
            ->orderBy('listened.listenedAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function deleteOutdated(ListenableEntityInterface $listened, User $user): void
    {
        $lastListened = $this->getListeningByDate($user, $listened::class);

        if (count($lastListened) <= 0) {
            return;
        }

        $toDelete = array_slice($lastListened, 9);
    
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
        $lastListened->setListenedAt(new \DateTimeImmutable());
        
        match (true) {
            $listened instanceof Music => $lastListened->setMusic($listened),
            $listened instanceof Album => $lastListened->setAlbum($listened),
            $listened instanceof Playlist => $lastListened->setPlaylist($listened),
        };

        $em = $this->getEntityManager();
        $em->persist($lastListened);
        $em->flush();
    }
}
