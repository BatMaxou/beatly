<?php

namespace App\Event\Subscriber;

use App\Entity\ArtistRequest;
use App\Enum\RequestStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'shouldRemove', entity: ArtistRequest::class)]
class ArtistRequestSubscriber
{
    private const SHOULD_REMOVE_STATUS = [
        RequestStatusEnum::ACCEPTED,
        RequestStatusEnum::DECLINED,
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function shouldRemove(ArtistRequest $artistRequest): void
    {
        if (!in_array($artistRequest->getStatus(), self::SHOULD_REMOVE_STATUS)) {
            return;
        }

        $this->em->remove($artistRequest);
        $this->em->flush();
    }
}
