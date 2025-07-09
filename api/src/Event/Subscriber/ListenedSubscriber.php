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

        match (true) {
            $listened instanceof Music => $this->handleMusic($listened, $event->user),
            default => $this->handle($listened, $event->user),
        };
    }

    private function handleMusic(Music $music, User $user): void
    {
        $music->listen();

        $this->handle($music, $user);
    }

    private function handle(ListenableEntityInterface $listened, User $user): void
    {
        $this->lastListenedRepository->deleteOutdated($listened, $user);
        $this->lastListenedRepository->create($listened, $user);
    }
}

