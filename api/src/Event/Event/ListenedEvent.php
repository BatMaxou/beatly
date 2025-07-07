<?php

namespace App\Event\Event;

use App\Entity\Interface\ListenableEntityInterface;
use App\Entity\User;

class ListenedEvent extends AbstractUserAwareEvent
{
    public function __construct(
        public User $user,
        public ListenableEntityInterface $listened,
    ) {
        parent::__construct($user);
    }
}
