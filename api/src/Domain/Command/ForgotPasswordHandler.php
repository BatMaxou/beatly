<?php

namespace App\Domain\Command;

use App\Domain\Command\Email\SendForgotPasswordEmailCommand;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function __invoke(ForgotPasswordCommand $command): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $command->email]);
        if (!$user) {
            // Result true car on veut quand même afficher le message de succès d'envoi si l'email existe
            return new JsonResponse(['error' => 'If email exists, we will send an you email', 'result' => true], Response::HTTP_BAD_REQUEST);
        }

        $token = Uuid::v4();
        $user->setResetToken($token);
        $this->em->flush();

        $this->bus->dispatch(new SendForgotPasswordEmailCommand(
            $user,
            $token,
        ));

        return new JsonResponse(['response' => 'If email exists, we will send an you email', 'result' => true], Response::HTTP_OK);
    }
}
