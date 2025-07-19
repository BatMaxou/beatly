<?php

namespace App\Domain\Command;

use App\Domain\Command\Email\SendAcceptArtistRequestEmailCommand;
use App\Enum\RequestStatusEnum;
use App\Event\Event\PromoteUserEvent;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class AcceptArtistRequestHandler
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
        private readonly ArtistRepository $artistRepository,
    ) {
    }

    public function __invoke(AcceptArtistRequestCommand $command): Response
    {
        $artistRequest = $command->artistRequest;
        $user = $artistRequest->getUser();

        $artistRequest->setStatus(RequestStatusEnum::ACCEPTED);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new PromoteUserEvent($user));

        $user = $this->artistRepository->find($user->getId());

        $this->bus->dispatch(new SendAcceptArtistRequestEmailCommand($user));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
