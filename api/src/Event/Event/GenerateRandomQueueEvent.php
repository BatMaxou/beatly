<?php

namespace App\Event\Event;

use App\Entity\User;

class GenerateRandomQueueEvent extends AbstractUserAwareEvent
{
    public function __construct(
        User $user,
        public int $currentPosition,
    ) {
        parent::__construct($user);
    }
}
