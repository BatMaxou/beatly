<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Music;
use App\Entity\Playlist;
use App\Enum\ApiReusableRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class MusicFilesProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $em,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Music
    {
        if (!$data instanceof Music) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', Music::class));
        }

        if (!ApiReusableRoute::UPDATE_MUSIC_FILES->value === $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $files = $this->requestStack->getCurrentRequest()->files->all();
        if (empty($files) || !isset($files['cover'])) {
            return $data;
        }

        $data->setCover($files['cover']);
        $this->em->flush();

        return $data;
    }
}

