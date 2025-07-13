<?php

namespace App\Event\Event;

use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\User;

class AddToQueueEvent extends AbstractUserAwareEvent
{
    public function __construct(
        public User $user,
        public ListenableEntityInterface $added,
    ) {
        parent::__construct($user);
    }
}
