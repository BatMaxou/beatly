<?php

namespace App\Domain\Command;

use App\Entity\FavoriteAlbum;
use App\Entity\FavoriteMusic;
use App\Entity\FavoritePlaylist;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddFavoriteHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(AddFavoriteCommand $command): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$command->music && !$command->playlist && !$command->album) {
            return new JsonResponse(['error' => 'No entity provided'], Response::HTTP_BAD_REQUEST);
        }

        $newFavorite = match (true) {
            null !== $command->music => (new FavoriteMusic())->setTarget($command->music),
            null !== $command->playlist => (new FavoritePlaylist())->setTarget($command->playlist),
            null !== $command->album => (new FavoriteAlbum())->setTarget($command->album),
            default => throw new \InvalidArgumentException('No valid entity provided'),
        };

        if (null !== $this->favoriteRepository->findFor($newFavorite->getTarget(), $user)) {
            return new JsonResponse(['error' => 'Entity already favourite'], Response::HTTP_CONFLICT);
        }

        $this->em->persist($newFavorite->setUser($user));
        $this->em->flush();

        return new Response(status: Response::HTTP_CREATED);
    }
}
