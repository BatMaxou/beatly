<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Enum\ApiReusableRoute;
use App\Service\QueueManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class QueueProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly QueueManager $queueManager,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!ApiReusableRoute::GET_QUEUE->value === $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('User must be authenticated to create a playlist');
        }

        $randomQueue = $user->getRandomQueue();
        if ($randomQueue) {
            return $randomQueue;
        }

        $this->queueManager->getQueue($user);
        $this->em->flush();

        return $user->getQueue();
    }
}
