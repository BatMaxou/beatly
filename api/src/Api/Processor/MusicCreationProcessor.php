<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Artist;
use App\Entity\Music;
use App\Enum\ApiReusableRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class MusicCreationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Music
    {
        if (!$data instanceof Music) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', Music::class));
        }

        if (ApiReusableRoute::CREATE_MUSIC->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('User must be authenticated to create a playlist');
        }

        if (!$user instanceof Artist) {
            throw new \LogicException('User must be an instance of Artist to create a music');
        }

        $data->setMainArtist($user);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
