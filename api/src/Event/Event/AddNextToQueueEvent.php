<?php

namespace App\Event\Event;

use App\Entity\Music;
use App\Entity\User;

class AddNextToQueueEvent extends AddToQueueEvent
{
    public function __construct(
        public User $user,
        public Music $music,
        public int $currentPosition,
    ) {
        parent::__construct($user, $music);
    }
}
