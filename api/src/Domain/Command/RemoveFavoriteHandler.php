<?php

namespace App\Domain\Command;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveFavoriteHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(RemoveFavoriteCommand $command): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$command->music && !$command->playlist && !$command->album) {
            return new JsonResponse(['error' => 'No entity provided'], Response::HTTP_BAD_REQUEST);
        }

        $toRemove = match (true) {
            null !== $command->music => $command->music,
            null !== $command->playlist => $command->playlist,
            null !== $command->album => $command->album,
            default => throw new \InvalidArgumentException('No valid entity provided'),
        };

        if (null === $favorite = $this->favoriteRepository->findFor($toRemove, $user)) {
            return new JsonResponse(['error' => 'Entity is not currently favourite'], Response::HTTP_CONFLICT);
        }

        $this->em->remove($favorite);
        $this->em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
