<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Queue;
use App\Entity\User;
use App\Enum\ApiReusableRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class QueueProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
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

        $queue = $user->getQueue();
        if (!$queue) {
            $queue = new Queue();
            $user->setQueue($queue);

            $this->em->persist($queue);
            $this->em->flush();
        }

        return $user->getQueue();
    }
}
