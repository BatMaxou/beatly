<?php

namespace App\Event\Event;

use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\User;

class AddNextToQueueEvent extends AddToQueueEvent
{
    public function __construct(
        User $user,
        ListenableEntityInterface $added,
        int $currentPosition,
    ) {
        parent::__construct($user, $added, $currentPosition);
    }
}
