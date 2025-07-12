<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Album;
use App\Enum\ApiReusableRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AlbumFilesProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Album
    {
        if (!$data instanceof Album) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', Album::class));
        }

        if (!ApiReusableRoute::UPDATE_ALBUM_FILES->value === $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $files = $this->requestStack->getCurrentRequest()->files->all();
        if (empty($files)) {
            return $data;
        }

        if (isset($files['cover'])) {
            $data->setCover($files['cover']);
        }

        if (isset($files['wallpaper'])) {
            $data->setWallpaper($files['wallpaper']);
        }

        $this->em->flush();

        return $data;
    }
}
