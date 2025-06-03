<?php

namespace App\Event\Subscriber;

use App\Entity\Music;
use App\Entity\User;
use App\Service\Client\EmbedderClient;
use App\Service\Client\QdrantClient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'upsert', entity: Music::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'upsert', entity: Music::class)]
#[AsEntityListener(event: Events::postRemove, method: 'remove', entity: Music::class)]
class StoreMusicSubscriber
{
    public function __construct(
        private readonly EmbedderClient $embedderClient,
        private readonly QdrantClient $qdrantClient,
    ) {
    }

    public function upsert(Music $music): void
    {
        $embedding = $this->embedderClient->embed($music->prepareForEmbedding());
        $this->qdrantClient->upsertMusic($music->getId(), $embedding, ['title' => $music->getTitle()]);
    }

    public function remove(Music $music): void
    {
        $this->qdrantClient->deleteMusic($music->getId());
    }
}

