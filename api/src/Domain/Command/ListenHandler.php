<?php

namespace App\Domain\Command;

use App\Event\Event\ListenedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ListenHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ListenCommand $command): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $isMusicLinked = null !== $command->playlist || null !== $command->album;

        $this->eventDispatcher->dispatch(new ListenedEvent($user, $command->music, $isMusicLinked));

        if ($command->playlist) {
            $this->eventDispatcher->dispatch(new ListenedEvent($user, $command->playlist));
        }

        if ($command->album) {
            $this->eventDispatcher->dispatch(new ListenedEvent($user, $command->album));
        }

        return new Response();
    }
}
