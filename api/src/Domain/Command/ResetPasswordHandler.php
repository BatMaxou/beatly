<?php

namespace App\Domain\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ResetPasswordHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): Response
    {
        $user = $this->userRepository->findOneBy(['resetToken' => $command->token]);
        if (!$user) {
            return new JsonResponse(['message' => 'Error during the process', 'result' => false], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($command->password);
        $user->setResetToken(null);
        $this->em->flush();

        return new JsonResponse(['message' => 'Password updated', 'result' => true], Response::HTTP_OK);
    }
}
