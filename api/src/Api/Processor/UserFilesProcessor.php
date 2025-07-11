<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Playlist;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserFilesProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof User) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', User::class));
        }

        $files = $this->requestStack->getCurrentRequest()->files->all();
        if (empty($files)) {
            return $data;
        }

        if (isset($files['avatar'])) {
            $data->setAvatar($files['avatar']);
        }

        if (isset($files['wallpaper'])) {
            $data->setWallpaper($files['wallpaper']);
        }

        $this->em->flush();

        return $data;
    }
}

