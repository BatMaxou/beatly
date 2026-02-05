<?php

namespace App\Event\Event;

use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\User;

class AddToQueueEvent extends AbstractUserAwareEvent
{
    public function __construct(
        User $user,
        public ListenableEntityInterface $added,
        public ?int $currentPosition = null,
    ) {
        parent::__construct($user);
    }
}
