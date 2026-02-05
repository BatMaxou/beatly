<?php

namespace App\Event\Subscriber;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Interface\EmbeddableEntityInterface;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Service\Client\EmbedderClient;
use App\Service\Client\QdrantClient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'upsert', entity: Music::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'upsert', entity: Music::class)]
#[AsEntityListener(event: Events::postRemove, method: 'remove', entity: Music::class)]
#[AsEntityListener(event: Events::postPersist, method: 'upsert', entity: Artist::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'upsert', entity: Artist::class)]
#[AsEntityListener(event: Events::postRemove, method: 'remove', entity: Artist::class)]
#[AsEntityListener(event: Events::postPersist, method: 'upsert', entity: Album::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'upsert', entity: Album::class)]
#[AsEntityListener(event: Events::postRemove, method: 'remove', entity: Album::class)]
#[AsEntityListener(event: Events::postPersist, method: 'upsert', entity: Playlist::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'upsert', entity: Playlist::class)]
#[AsEntityListener(event: Events::postRemove, method: 'remove', entity: Playlist::class)]
class StoreEmbeddableEntitySubscriber
{
    public function __construct(
        private readonly EmbedderClient $embedderClient,
        private readonly QdrantClient $qdrantClient,
    ) {
    }

    public function upsert(EmbeddableEntityInterface $entity): void
    {
        $payload = $this->buildPayload($entity);

        foreach ($entity->supportEmbedding() as $embeddingType) {
            $embedding = $this->embedderClient->embed($entity->prepareForEmbedding($embeddingType));
            $this->qdrantClient->upsert($entity->getUuid(), $embeddingType, $embedding, $payload);
        }
    }

    public function remove(EmbeddableEntityInterface $entity): void
    {
        foreach ($entity->supportEmbedding() as $embeddingType) {
            $this->qdrantClient->delete($entity->getUuid(), $embeddingType);
        }
    }

    private function buildPayload(EmbeddableEntityInterface $entity): array
    {
        return match (true) {
            $entity instanceof Music => $this->buildMusicPayload($entity),
            $entity instanceof Artist => $this->buildArtistPayload($entity),
            $entity instanceof Album => $this->buildAlbumPayload($entity),
            $entity instanceof Playlist => $this->buildPlaylistPayload($entity),
            default => throw new \InvalidArgumentException('Unsupported entity type for qdrant payload'),
        };
    }

    private function buildCommonPayload(EmbeddableEntityInterface $entity): array
    {
        return [
            'uuid' => $entity->getUuid(),
            'class_identifier' => $entity::getClassIdentifier(),
        ];
    }

    private function buildMusicPayload(Music $music): array
    {
        $mainArtist = $music->getMainArtist();

        return [
            ...$this->buildCommonPayload($music),
            'title' => $music->getTitle(),
            ...($mainArtist ? ['artist' => $mainArtist->getName()] : []),
        ];
    }

    private function buildArtistPayload(Artist $artist): array
    {
        return [
            ...$this->buildCommonPayload($artist),
            'name' => $artist->getName(),
        ];
    }

    private function buildAlbumPayload(Album $album): array
    {
        $artist = $album->getArtist();

        return [
            ...$this->buildCommonPayload($album),
            'title' => $album->getTitle(),
            ...($artist ? ['artist' => $artist->getName()] : []),
        ];
    }

    private function buildPlaylistPayload(Playlist $playlist): array
    {
        $creator = $playlist->getCreator();

        return [
            ...$this->buildCommonPayload($playlist),
            'title' => $playlist->getTitle(),
            ...($creator ? ['creator' => $creator->getName()] : []),
        ];
    }
}
