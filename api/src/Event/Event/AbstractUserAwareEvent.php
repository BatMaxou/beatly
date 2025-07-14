<?php

namespace App\Event\Event;

use App\Entity\User;
use App\Event\Event\Interface\UserEvent;

abstract class AbstractUserAwareEvent implements UserEvent
{
    public function __construct(
        private User $user,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
