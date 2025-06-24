<?php

namespace App\Domain\Command;

use App\Domain\Command\Email\SendForgotPasswordEmailCommand;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class ForgotPasswordHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(ForgotPasswordCommand $command): Response {
        $user = $this->userRepository->findOneBy(['email' => $command->email]);
        if (!$user) {
            return new Response();
        }

        $token = Uuid::v4();
        $user->setResetToken($token);
        $this->em->flush();

        $this->bus->dispatch(new SendForgotPasswordEmailCommand(
            $user,
            $token,
        ));
        
        return new Response();
    }
}
