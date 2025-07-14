<?php

namespace App\Domain\Command;

use App\Entity\User;
use App\Event\Event\AddMultipleNextToQueueEvent;
use App\Event\Event\AddMultipleToQueueEvent;
use App\Event\Event\AddNextToQueueEvent;
use App\Event\Event\AddToQueueEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class AddToQueueHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(AddToQueueCommand $command): Response
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        if (
            null === $command->music
            && null === $command->playlist
            && null === $command->album
            && null === $command->musics
        ) {
            return new JsonResponse(['error' => 'No item to add to queue'], Response::HTTP_BAD_REQUEST);
        }

        $shouldBeNext = $command->shouldBeNext;
        $currentPosition = $command->currentPosition;
        if ($shouldBeNext && null === $currentPosition) {
            return new JsonResponse(['error' => 'Current position must be set when adding to queue as next'], Response::HTTP_BAD_REQUEST);
        }

        $this->eventDispatcher->dispatch(match (true) {
            null !== $command->music => $shouldBeNext
                ? new AddNextToQueueEvent($user, $command->music, $currentPosition)
                : new AddToQueueEvent($user, $command->music, $currentPosition),
            null !== $command->musics => $shouldBeNext
                ? new AddMultipleNextToQueueEvent($user, $command->musics, $currentPosition)
                : new AddMultipleToQueueEvent($user, $command->musics, $currentPosition),
            null !== $command->playlist => $shouldBeNext
                ? new AddNextToQueueEvent($user, $command->playlist, $currentPosition)
                : new AddToQueueEvent($user, $command->playlist, $currentPosition),
            null !== $command->album => $shouldBeNext
                ? new AddNextToQueueEvent($user, $command->album, $currentPosition)
                : new AddToQueueEvent($user, $command->album, $currentPosition),
            default => throw new \InvalidArgumentException('No valid item to add to queue'),
        });

        return new JsonResponse(
            $this->serializer->serialize($user->getRandomQueue() ?? $user->getQueue(), 'json', ['groups' => ['queue:read']]),
            Response::HTTP_CREATED,
            [],
            true,
        );
    }
}
