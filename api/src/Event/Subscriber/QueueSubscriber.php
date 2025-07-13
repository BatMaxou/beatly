<?php

namespace App\Event\Subscriber;

use App\Entity\Album;
use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Entity\Queue;
use App\Entity\QueueItem;
use App\Entity\User;
use App\Event\Event\AddMultipleNextToQueueEvent;
use App\Event\Event\AddMultipleToQueueEvent;
use App\Event\Event\AddNextToQueueEvent;
use App\Event\Event\AddToQueueEvent;
use App\Event\Event\ResetQueueEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QueueSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddToQueueEvent::class => 'onAdd',
            AddNextToQueueEvent::class => 'onAddNext',
            AddMultipleToQueueEvent::class => 'onAddMultiple',
            AddMultipleNextToQueueEvent::class => 'onAddMultipleNext',
            ResetQueueEvent::class => 'onReset',
        ];
    }

    public function onAdd(AddToQueueEvent $event): void
    {
        $added = $event->added;
        $user = $event->user;
        $queue = $this->getQueue($user);

        $this->addToQueue($queue, $added);

        $this->em->flush();
    }

    public function onAddNext(AddNextToQueueEvent $event): void
    {
        $user = $event->user;
        $queue = $this->getQueue($user);

        $this->addNextToQueue(
            $queue,
            [$event->added],
            $event->currentPosition
        );

        $this->em->flush();
    }

    public function onAddMultiple(AddMultipleToQueueEvent $event): void
    {
        $user = $event->user;
        $queue = $this->getQueue($user);

        foreach ($event->added as $added) {
            $this->addToQueue($queue, $added);
        }

        $this->em->flush();
    }

    public function onAddMultipleNext(AddMultipleNextToQueueEvent $event): void
    {
        $user = $event->user;
        $queue = $this->getQueue($user);

        $this->addNextToQueue(
            $queue,
            $event->added,
            $event->currentPosition
        );

        $this->em->flush();
    }

    public function onReset(ResetQueueEvent $event): void
    {
        $user = $event->user;
        $queue = $this->getQueue($user);
        $queue->clearQueueItems();

        $this->em->flush();
    }

    private function getQueue(User $user): Queue
    {
        $queue = $user->getQueue();
        if ($queue) {
            return $queue;
        }

        $queue = new Queue();
        $user->setQueue($queue);

        $this->em->persist($queue);

        return $queue;
    }

    private function addToQueue(Queue $queue, ListenableEntityInterface $added): void
    {
        match (true) {
            $added instanceof Music => $this->addMusicToQueue($queue, $added),
            $added instanceof Playlist => $this->addPlaylistToQueue($queue, $added),
            $added instanceof Album => $this->addAlbumToQueue($queue, $added),
            default => throw new \InvalidArgumentException('Unsupported entity type for queue addition.'),
        };
    }

    private function addMusicToQueue(Queue $queue, Music $music): void
    {
        $queue->addQueueItem(
            (new QueueItem())
            ->setMusic($music)
            ->setPosition($queue->getNextPosition())
        );

        $this->em->flush();
    }

    private function addPlaylistToQueue(Queue $queue, Playlist $playlist): void
    {
        $playlistMusics = $playlist->getMusics()->toArray();
        usort($playlistMusics, function ($a, $b) {
            return $a->getAddedAt() <=> $b->getAddedAt();
        });

        foreach ($playlistMusics as $playlistMusic) {
            $this->addMusicToQueue($queue, $playlistMusic->getMusic());
        }
    }

    private function addAlbumToQueue(Queue $queue, Album $album): void
    {
        $albumMusics = $album->getMusics()->toArray();
        usort($albumMusics, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        foreach ($albumMusics as $albumMusic) {
            $this->addMusicToQueue($queue, $albumMusic->getMusic());
        }
    }

    /**
     * @param Music[] $musics
     */
    private function addNextToQueue(Queue $queue, array $musics, int $currentPosition): void
    {
        $nbToAdd = count($musics);
        $startPosition = $currentPosition + 1;
        foreach ($queue->getQueueItems() as $item) {
            if ($item->getPosition() > $currentPosition) {
                $item->setPosition($item->getPosition() + $nbToAdd);
            }
        }

        foreach ($musics as $music) {
            $queue->addQueueItem(
                (new QueueItem())
                ->setMusic($music)
                ->setPosition($startPosition)
            );
            ++$startPosition;
        }
    }
}
