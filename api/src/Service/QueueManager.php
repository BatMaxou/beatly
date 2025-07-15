<?php

namespace App\Service;

use App\Entity\Album;
use App\Entity\AlbumMusic;
use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Entity\PlaylistMusic;
use App\Entity\Queue;
use App\Entity\QueueBase;
use App\Entity\QueueItem;
use App\Entity\RandomQueue;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class QueueManager
{
    public function __construct(
        public readonly EntityManagerInterface $em,
    ) {
    }

    public function getQueue(User $user): Queue
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

    public function addToQueue(QueueBase $queue, ListenableEntityInterface $added): void
    {
        match (true) {
            $added instanceof Music => $this->addMusicsToQueue($queue, [$added]),
            $added instanceof Playlist => $this->addPlaylistToQueue($queue, $added),
            $added instanceof Album => $this->addAlbumToQueue($queue, $added),
            default => throw new \InvalidArgumentException('Unsupported entity type for queue addition.'),
        };
    }

    public function addNextToQueue(QueueBase $queue, ListenableEntityInterface $added, int $currentPosition): void
    {
        match (true) {
            $added instanceof Music => $this->addNextMusicsToQueue($queue, [$added], $currentPosition),
            $added instanceof Playlist => $this->addPlaylistToQueue($queue, $added, $currentPosition),
            $added instanceof Album => $this->addAlbumToQueue($queue, $added, $currentPosition),
            default => throw new \InvalidArgumentException('Unsupported entity type for next queue addition.'),
        };
    }

    public function addRandomToQueue(QueueBase $queue, ListenableEntityInterface $added, int $currentPosition): void
    {
        match (true) {
            $added instanceof Music => $this->addRandomMusicsToQueue($queue, [$added], $currentPosition),
            $added instanceof Playlist => $this->addRandomPlaylistToQueue($queue, $added, $currentPosition),
            $added instanceof Album => $this->addRandomAlbumToQueue($queue, $added, $currentPosition),
            default => throw new \InvalidArgumentException('Unsupported entity type for random queue addition.'),
        };
    }

    /**
     * @param Music[] $musics
     */
    public function addMusicsToQueue(QueueBase $queue, array $musics): void
    {
        foreach ($musics as $music) {
            $queue->addQueueItem(
                new QueueItem()
                ->setMusic($music)
                ->setPosition($queue->getNextPosition())
            );
        }

        $this->em->flush();
    }

    /**
     * @param Music[] $musics
     */
    public function addNextMusicsToQueue(QueueBase $queue, array $musics, int $currentPosition): void
    {
        $nbToAdd = count($musics);
        $startPosition = $currentPosition + 1;
        $this->reorderAfterPosition($queue, $currentPosition, $nbToAdd);

        foreach ($musics as $music) {
            $queue->addQueueItem(
                new QueueItem()
                ->setMusic($music)
                ->setPosition($startPosition)
            );
            ++$startPosition;
        }
    }

    /**
     * @param Music[] $musics
     */
    public function addRandomMusicsToQueue(QueueBase $queue, array $musics, int $currentPosition): void
    {
        $currentTotalItems = count($queue->getQueueItems());

        foreach ($musics as $music) {
            $randomPosition = rand($currentPosition + 1, $currentTotalItems + 1);
            $this->reorderAfterPosition($queue, $randomPosition - 1);

            $queue->addQueueItem(
                new QueueItem()
                ->setMusic($music)
                ->setPosition($randomPosition)
            );

            ++$currentTotalItems;
        }

        $this->em->flush();
    }

    public function addPlaylistToQueue(QueueBase $queue, Playlist $playlist, ?int $currentPosition = null): void
    {
        $playlistMusics = $playlist->getMusics()->toArray();
        usort($playlistMusics, function ($a, $b) {
            return $a->getAddedAt() <=> $b->getAddedAt();
        });

        $callback = null === $currentPosition ? $this->addMusicsToQueue(...) : $this->addNextMusicsToQueue(...);

        $callback(
            $queue,
            array_map(fn ($music) => $music->getMusic(), $playlistMusics),
            $currentPosition
        );
    }

    public function addRandomPlaylistToQueue(QueueBase $queue, Playlist $playlist, int $currentPosition): void
    {
        $this->addRandomMusicsToQueue(
            $queue,
            $playlist->getMusics()->map(fn (PlaylistMusic $playlistMusic) => $playlistMusic->getMusic())->toArray(),
            $currentPosition
        );
    }

    public function addAlbumToQueue(QueueBase $queue, Album $album, ?int $currentPosition = null): void
    {
        $albumMusics = $album->getMusics()->toArray();
        usort($albumMusics, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        $callback = null === $currentPosition ? $this->addMusicsToQueue(...) : $this->addNextMusicsToQueue(...);

        $callback(
            $queue,
            array_map(fn ($music) => $music->getMusic(), $albumMusics),
            $currentPosition
        );
    }

    public function addRandomAlbumToQueue(QueueBase $queue, Album $album, int $currentPosition): void
    {
        $this->addRandomMusicsToQueue(
            $queue,
            $album->getMusics()->map(fn (AlbumMusic $albumMusic) => $albumMusic->getMusic())->toArray(),
            $currentPosition
        );
    }

    public function randomizeQueue(Queue $queue, int $currentPosition): void
    {
        $toAdd = [];
        $randomQueue = new RandomQueue();
        foreach ($queue->getQueueItems() as $item) {
            if ($item->getPosition() === $currentPosition) {
                $randomQueue->addQueueItem(
                    new QueueItem()
                    ->setMusic($item->getMusic())
                    ->setPosition(1)
                );
            } else {
                $toAdd[] = $item->getMusic();
            }
        }

        shuffle($toAdd);
        $this->addMusicsToQueue($randomQueue, $toAdd);
        $queue->getUser()->setRandomQueue($randomQueue);

        $this->em->flush();
    }

    private function reorderAfterPosition(QueueBase $queue, int $position, int $nbToAdd = 1): void
    {
        foreach ($queue->getQueueItems() as $item) {
            if ($item->getPosition() > $position) {
                $item->setPosition($item->getPosition() + $nbToAdd);
            }
        }
    }
}
