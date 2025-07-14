<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Playlist;
use App\Enum\ApiReusableRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class PlaylistCreationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Playlist
    {
        if (!$data instanceof Playlist) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', Playlist::class));
        }

        if (ApiReusableRoute::CREATE_PLAYLIST->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('User must be authenticated to create a playlist');
        }

        $data->setCreator($user);

        $files = $this->requestStack->getCurrentRequest()->files->all();
        if (empty($files)) {
            if (isset($files['cover'])) {
                $data->setCover($files['cover']);
            }

            if (isset($files['wallpaper'])) {
                $data->setWallpaper($files['wallpaper']);
            }
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
