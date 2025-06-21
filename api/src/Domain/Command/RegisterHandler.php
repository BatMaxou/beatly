<?php

namespace App\Domain\Command;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RegisterHandler {

    public function __construct(
        private UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(RegisterCommand $command): Response {
        if ($this->userRepository->findOneBy(['email' => $command->email])) {
            return new JsonResponse(['error' => 'Email already exists'], 409);
        }

        $newUser = (new User())
            ->setEmail($command->email)
            ->setName($command->name)
            ->setPassword($command->password);

        if ($command->registerType === RegisterCommand::ARTIST_REGISTER) {
            $newUser->addRole(RoleEnum::ARTIST);
        }

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return new JsonResponse(['response' => 'User created'], 201);
    }
}
