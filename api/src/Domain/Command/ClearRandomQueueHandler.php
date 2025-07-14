<?php

namespace App\Domain\Command;

use App\Event\Event\ClearRandomQueueEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ClearRandomQueueHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ClearRandomQueueCommand $command): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $this->eventDispatcher->dispatch(new ClearRandomQueueEvent($user));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
