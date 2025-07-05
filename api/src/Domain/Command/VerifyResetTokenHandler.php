<?php

namespace App\Domain\Command;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerifyResetTokenHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function __invoke(VerifyResetTokenCommand $command): Response {
        $user = $this->userRepository->findOneBy(['resetToken' => $command->token]);
        
        if (!$user) {
            return new JsonResponse(['result' => false], Response::HTTP_OK);
        }

        return new JsonResponse(['result' => true], Response::HTTP_OK);
    }
}
