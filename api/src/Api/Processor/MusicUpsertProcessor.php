<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Artist;
use App\Entity\Music;
use App\Enum\ApiReusableRoute;
use App\Service\Mp3MetadataManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Vich\UploaderBundle\Storage\StorageInterface;

class MusicUpsertProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly StorageInterface $storage,
        private readonly Mp3MetadataManager $mp3MetadataManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Music
    {
        if (!$data instanceof Music) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', Music::class));
        }

        if (false === in_array($operation->getName(), [
            ApiReusableRoute::CREATE_MUSIC->value,
            ApiReusableRoute::UPDATE_MUSIC->value,
        ])) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('User must be authenticated to create a playlist');
        }

        if (!$user instanceof Artist) {
            throw new \LogicException('User must be an instance of Artist to create a music');
        }

        if ($operation->getName() === ApiReusableRoute::CREATE_MUSIC->value) {
            $data->setMainArtist($user);
        }

        $filePath = $this->storage->resolvePath($data->getFile(), 'file');
        if (!$filePath) {
            throw new \LogicException('Mp3 file is required to create a music');
        }

        $data->setDuration($this->mp3MetadataManager->getFor($filePath)['duration']);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
