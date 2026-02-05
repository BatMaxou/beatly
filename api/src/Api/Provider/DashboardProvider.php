<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Dashboard;
use App\Enum\ApiReusableRoute;
use App\Repository\CategoryRepository;
use App\Repository\LastListenedRepository;
use App\Repository\MusicRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly LastListenedRepository $lastListenedRepository,
        private readonly MusicRepository $musicRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PlaylistRepository $playlistRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (ApiReusableRoute::GET_DASHBOARD->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            throw new \LogicException('User must be authenticated to access the dashboard.');
        }

        return new Dashboard(
            $this->lastListenedRepository->findByUser($currentUser),
            $this->categoryRepository->findMostPopular(),
            $this->musicRepository->findMostLiked(),
            $this->musicRepository->findMostListened(),
            $this->playlistRepository->findMostLiked(),
        );
    }
}
