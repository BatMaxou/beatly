<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Enum\ApiReusableRoute;
use Symfony\Bundle\SecurityBundle\Security;

class MeProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
        if (!ApiReusableRoute::ME->value === $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        return $this->security->getUser();
    }
}
