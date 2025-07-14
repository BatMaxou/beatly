<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Recommendation;
use App\Enum\ApiReusableRoute;
use App\Repository\LastMusicListenedRepository;
use App\Repository\MusicRepository;
use App\Service\Client\EmbedderClient;
use App\Service\Client\OllamaClient;
use App\Service\Client\QdrantClient;
use App\Service\Prompt\PromptBuilder;
use App\Service\Prompt\PromptEnum;
use Symfony\Bundle\SecurityBundle\Security;

class RecommendationProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly LastMusicListenedRepository $lastMusicListenedRepository,
        private readonly MusicRepository $musicRepository,
        private readonly QdrantClient $qdrantClient,
        private readonly EmbedderClient $embedderClient,
        private readonly PromptBuilder $promptBuilder,
        private readonly OllamaClient $ollamaClient,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (ApiReusableRoute::GET_RECOMMENDATIONS->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            throw new \LogicException('User must be authenticated to access the dashboard.');
        }

        $lastMusicsListened = $this->lastMusicListenedRepository->findByUser($currentUser);
        if (empty($lastMusicsListened)) {
            return new Recommendation([]);
        }

        $qdrantRecommendations = $this->qdrantClient->recommend(array_map(
            fn ($lastMusic) => $lastMusic->getTarget()->getId(),
            $lastMusicsListened
        ), 20);

        $references = $this->buildReferences($lastMusicsListened);
        $catalog = $this->buildCatalog($qdrantRecommendations);
        $prompt = $this->buildPrompt($references, $catalog);

        $response = $this->ollamaClient->ask($prompt);

        $matches = [];
        preg_match_all('/\d+/', $response, $matches);
        $recommendations = $this->musicRepository->findBy(['id' => $matches[0]]);

        return new Recommendation($recommendations);
    }

    private function buildReferences(array $lastMusicsListened): array
    {
        return array_map(
            fn ($item) => \sprintf(
                '%s by %s (id: %s)',
                $item->getTarget()->getTitle(),
                $item->getTarget()->getArtists()->first() ? $item->getTarget()->getArtists()->first()->getName() : 'Unknown',
                $item->getTarget()->getId()
            ),
            $lastMusicsListened,
        );
    }

    private function buildCatalog(array $items): array
    {
        return array_map(
            fn ($item) => \sprintf(
                '- %s by %s (id: %s)',
                $item['payload']['title'],
                $item['payload']['artist'] ?? 'Unknown',
                $item['id'],
            ),
            $items,
        );
    }

    private function buildPrompt(array $references, array $catalog): string
    {
        return $this->promptBuilder->build(
            PromptEnum::MUSIC_RECOMMENDATIONS,
            [
                '{{ references }}' => implode("\n", $references),
                '{{ catalog }}' => implode("\n", $catalog),
            ]
        );
    }
}
