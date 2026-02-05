<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Search;
use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Enum\ApiReusableRoute;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\MusicRepository;
use App\Repository\PlaylistRepository;
use App\Service\Client\EmbedderClient;
use App\Service\Client\QdrantClient;

class SearchProvider implements ProviderInterface
{
    public function __construct(
        private readonly QdrantClient $qdrantClient,
        private readonly EmbedderClient $embedderClient,
        private readonly MusicRepository $musicRepository,
        private readonly ArtistRepository $artistRepository,
        private readonly AlbumRepository $albumRepository,
        private readonly PlaylistRepository $playlistRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (ApiReusableRoute::SEARCH->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        if (!isset($context['filters']) || !isset($context['filters']['query'])) {
            throw new \InvalidArgumentException('Query parameter is required');
        }

        $query = $this->embedderClient->embed($context['filters']['query']);

        $results = [];
        $qdrantSearch = $this->qdrantClient->search($query);
        foreach ($qdrantSearch as $result) {
            $payload = $result['payload'] ?? [];
            $classIdentifier = $payload['class_identifier'] ?? null;
            $results[] = match ($classIdentifier) {
                Music::getClassIdentifier() => $this->musicRepository->findOneBy(['uuid' => $payload['uuid']]),
                Artist::getClassIdentifier() => $this->artistRepository->findOneBy(['uuid' => $payload['uuid']]),
                Album::getClassIdentifier() => $this->albumRepository->findOneBy(['uuid' => $payload['uuid']]),
                Playlist::getClassIdentifier() => $this->playlistRepository->findOneBy(['uuid' => $payload['uuid']]),
                default => throw new \InvalidArgumentException(sprintf('Unsupported class identifier "%s"', $classIdentifier)),
            };
        }

        return new Search($results);
    }
}
