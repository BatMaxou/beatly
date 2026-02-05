<?php

namespace App\Event\Subscriber;

use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\Music;
use App\Entity\User;
use App\Event\Event\ListenedEvent;
use App\Repository\LastListenedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ListenedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LastListenedRepository $lastListenedRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ListenedEvent::class => 'onListened',
        ];
    }

    public function onListened(ListenedEvent $event): void
    {
        $listened = $event->listened;
        $user = $event->getUser();

        match (true) {
            $listened instanceof Music => $this->handleMusic($listened, $user, $event->isLinked),
            default => $this->handle($listened, $user, $event->isLinked),
        };

        $this->lastListenedRepository->deleteOutdated($user);
    }

    private function handleMusic(Music $music, User $user, bool $isLinked): void
    {
        $music->listen();

        $this->handle($music, $user, $isLinked);
    }

    private function handle(ListenableEntityInterface $listened, User $user, bool $isLinked): void
    {
        if ($isLinked) {
            return;
        }

        $this->lastListenedRepository->deleteDuplicates($listened, $user);
        $this->lastListenedRepository->create($listened, $user);
    }
}
