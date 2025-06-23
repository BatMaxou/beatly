<?php

namespace App\Domain\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class ResetPasswordHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): Response {
        $user = $this->userRepository->findOneBy(['resetToken' => $command->token]);
        if (!$user) {
            return new Response();
        }

        $user->setPassword($command->password);
        $user->setResetToken(null);
        $this->em->flush();

        return new Response();
    }
}
