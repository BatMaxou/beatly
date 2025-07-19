<?php

namespace App\Event\Subscriber;

use App\Entity\Artist;
use App\Entity\User;
use App\Event\Event\PromoteUserEvent;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ArtistRepository $artistRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PromoteUserEvent::class => 'onPromote',
        ];
    }

    public function onPromote(PromoteUserEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User || $user instanceof Artist) {
            throw new \InvalidArgumentException('User must be an instance of User and not already an Artist');
        }

        $this->em->getConnection()->beginTransaction();

        try {
            $this->em->getConnection()->executeStatement(
                'INSERT INTO artist (id, uuid) VALUES (?, ?)',
                [$user->getId(), ''],
            );

            $this->em->getConnection()->executeStatement(
                'UPDATE user SET discr = ? WHERE id = ?',
                ['artist', $user->getId()],
            );

            $this->em->detach($user);

            $artist = $this->artistRepository->find($user->getId());
            if (!$artist) {
                throw new \RuntimeException('Failed to promote user to artist');
            }

            $artist->init();

            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();

            throw $e;
        }
    }
}
