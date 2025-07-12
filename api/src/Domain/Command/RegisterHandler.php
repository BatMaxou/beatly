<?php

namespace App\Domain\Command;

use App\Entity\Artist;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RegisterHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(RegisterCommand $command): Response
    {
        if ($this->userRepository->findOneBy(['email' => $command->email])) {
            return new JsonResponse(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
        }

        $entity = match ($command->registerType) {
            RegisterCommand::ARTIST_REGISTER => new Artist(),
            RegisterCommand::USER_REGISTER => new User(),
            default => null,
        };

        if (!$entity) {
            return new JsonResponse(['error' => 'Invalid registration type'], Response::HTTP_BAD_REQUEST);
        }

        $newUser = $entity
            ->setEmail($command->email)
            ->setName($command->name)
            ->setPassword($command->password);

        $this->em->persist($newUser);
        $this->em->flush();

        return new JsonResponse(['response' => 'User created', 'result' => true], Response::HTTP_CREATED);
    }
}
