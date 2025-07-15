<?php

namespace App\Domain\Command;

use App\Entity\User;
use App\Event\Event\GenerateRandomQueueEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class GenerateRandomQueueHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(GenerateRandomQueueCommand $command): Response
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $this->eventDispatcher->dispatch(new GenerateRandomQueueEvent($user, $command->currentPosition));

        return new JsonResponse(
            $this->serializer->serialize($user->getRandomQueue(), 'jsonld', ['groups' => ['queue:read']]),
            Response::HTTP_CREATED,
            [],
            true,
        );
    }
}
