<?php

namespace App\Event\Event;

use App\Entity\User;

abstract class AbstractUserAwareEvent
{
    public function __construct(
        public User $user,
    ) {
    }
}
