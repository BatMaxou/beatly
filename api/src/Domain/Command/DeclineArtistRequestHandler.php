<?php

namespace App\Domain\Command;

use App\Domain\Command\Email\SendDeclineArtistRequestEmailCommand;
use App\Enum\RequestStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class DeclineArtistRequestHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(DeclineArtistRequestCommand $command): Response
    {
        $artistRequest = $command->artistRequest;
        $user = $artistRequest->getUser();

        $artistRequest->setStatus(RequestStatusEnum::DECLINED);
        $this->em->flush();

        $this->bus->dispatch(new SendDeclineArtistRequestEmailCommand($user));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
