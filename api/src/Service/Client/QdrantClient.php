<?php

namespace App\Service\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class QdrantClient
{
    private HttpClientInterface $client;
    private string $baseUrl;

    public function __construct(HttpClientInterface $client, string $baseUrl)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    public function initMusicCollection(): void
    {
        $response = $this->client->request('PUT', sprintf('%s/collections/music_catalog', $this->baseUrl), [
            'json' => [
                'vectors' => [
                    'size' => 384,
                    'distance' => 'Cosine',
                ],
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to create collection');
        }
    }

    public function removeMusicCollection(): void
    {
        $response = $this->client->request('DELETE', sprintf('%s/collections/music_catalog', $this->baseUrl));

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to delete collection');
        }
    }

    public function upsertMusic(int $id, array $embedding, array $payload): ResponseInterface
    {
        if (384 !== count($embedding)) {
            throw new \InvalidArgumentException(sprintf('Embedding must have 384 dimensions, got %d', count($embedding)));
        }

        $response = $this->client->request('PUT', sprintf('%s/collections/music_catalog/points', $this->baseUrl), [
            'json' => [
                'points' => [[
                    'id' => $id,
                    'vector' => array_values($embedding),
                    'payload' => $payload,
                ]],
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to upsert point');
        }

        return $response;
    }

    public function deleteMusic(int $id): ResponseInterface
    {
        $response = $this->client->request('POST', sprintf('%s/collections/music_catalog/points/delete', $this->baseUrl), [
            'json' => [
                'points' => [$id],
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to delete point');
        }

        return $response;
    }

    public function recommend(array $embeddings, int $top = 20): array
    {
        $response = $this->client->request('POST', sprintf('%s/collections/music_catalog/points/query', $this->baseUrl), [
            'json' => [
                'query' => [
                    'recommend' => [
                        'positive' => $embeddings,
                    ],
                ],
                'limit' => $top,
                'with_payload' => true,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to search: '.$response->getContent(false));
        }

        return $response->toArray()['result']['points'] ?? [];
    }

    public function getMusicCollectionInfo(): array
    {
        $response = $this->client->request('GET', sprintf('%s/collections/music_catalog', $this->baseUrl));

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to get collection info: '.$response->getContent(false));
        }

        return $response->toArray();
    }

    public function getMusicPointCount(): int
    {
        $response = $this->client->request('POST', sprintf('%s/collections/music_catalog/points/count', $this->baseUrl), [
            'json' => (object) [], // Corps vide mais valide
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed to count points: '.$response->getContent(false));
        }

        $data = $response->toArray();

        return $data['result']['count'] ?? 0;
    }
}
