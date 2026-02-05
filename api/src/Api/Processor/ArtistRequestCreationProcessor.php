<?php

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Artist;
use App\Entity\ArtistRequest;
use App\Entity\User;
use App\Enum\ApiReusableRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ArtistRequestCreationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ArtistRequest
    {
        if (!$data instanceof ArtistRequest) {
            throw new \InvalidArgumentException(\sprintf('Data must be an instance of %s', ArtistRequest::class));
        }

        if (ApiReusableRoute::CREATE_ARTIST_REQUEST->value !== $operation->getName()) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('User must be authenticated to create an artist request');
        }

        if ($user instanceof Artist || !$user instanceof User) {
            throw new \LogicException('User must be an instance of User to create an artist request');
        }

        if (null !== $user->getArtistRequest()) {
            throw new \InvalidArgumentException('User already sent an artist request');
        }

        $data->setUser($user);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
