<?php

namespace App\Event\Subscriber;

use App\Entity\Album;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Entity\Queue;
use App\Entity\QueueItem;
use App\Entity\User;
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
            ResetQueueEvent::class => 'onReset',
        ];
    }

    public function onAdd(AddToQueueEvent $event): void
    {
        $added = $event->added;
        $user = $event->user;
        $queue = $this->getQueue($user);

        match (true) {
            $added instanceof Music => $this->addMusicToQueue($queue, $added),
            $added instanceof Playlist => $this->addPlaylistToQueue($queue, $added),
            $added instanceof Album => $this->addAlbumToQueue($queue, $added),
            default => throw new \InvalidArgumentException('Unsupported entity type for queue addition.'),
        };

        $this->em->flush();
    }

    public function onAddNext(AddNextToQueueEvent $event): void
    {
        $user = $event->user;
        $queue = $this->getQueue($user);

        $this->addNextMusicToQueue(
            $queue,
            $event->music,
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

    private function addNextMusicToQueue(Queue $queue, Music $music, int $currentPosition): void
    {
        foreach ($queue->getQueueItems() as $item) {
            if ($item->getPosition() > $currentPosition) {
                $item->setPosition($item->getPosition() + 1);
            }
        }

        $queue->addQueueItem(
            (new QueueItem())
            ->setMusic($music)
            ->setPosition($currentPosition + 1)
        );
    }
}
