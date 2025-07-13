<?php

namespace App\Domain\Command;

use App\Entity\User;
use App\Event\Event\AddMultipleNextToQueueEvent;
use App\Event\Event\AddMultipleToQueueEvent;
use App\Event\Event\AddNextToQueueEvent;
use App\Event\Event\AddToQueueEvent;
use App\Event\Event\ResetQueueEvent;
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

        if ($command->shouldBeNext && null === $command->currentPosition) {
            return new JsonResponse(['error' => 'Current position must be set when adding to queue as next'], Response::HTTP_BAD_REQUEST);
        }

        if (null === $command->music && null === $command->musics) {
            $this->eventDispatcher->dispatch(new ResetQueueEvent($user));
        }

        $this->eventDispatcher->dispatch(match (true) {
            null !== $command->music => $command->shouldBeNext
                ? new AddNextToQueueEvent($user, $command->music, $command->currentPosition)
                : new AddToQueueEvent($user, $command->music),
            null !== $command->musics => $command->shouldBeNext
                ? new AddMultipleNextToQueueEvent($user, $command->musics, $command->currentPosition)
                : new AddMultipleToQueueEvent($user, $command->musics),
            null !== $command->playlist => new AddToQueueEvent($user, $command->playlist),
            null !== $command->album => new AddToQueueEvent($user, $command->album),
            default => throw new \InvalidArgumentException('No valid item to add to queue'),
        });

        return new JsonResponse(
            $this->serializer->serialize($user->getQueue(), 'json', ['groups' => ['queue:read']]),
            Response::HTTP_CREATED,
            [],
            true,
        );
    }
}
