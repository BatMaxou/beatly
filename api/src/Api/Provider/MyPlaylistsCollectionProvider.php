<?php

namespace App\Api\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Enum\ApiReusableRoute;
use Symfony\Bundle\SecurityBundle\Security;

final class MyPlaylistsCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly CollectionProvider $collectionProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (ApiReusableRoute::GET_COLLECTION_MY_PLAYLISTS->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('User must be authenticated to access these ressources.');
        }

        $context['filters']['creator'] = $user->getId();

        return $this->collectionProvider->provide($operation, $uriVariables, $context);
    }
}
