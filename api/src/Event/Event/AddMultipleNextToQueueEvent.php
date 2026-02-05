<?php

namespace App\Event\Event;

use App\Entity\Music;
use App\Entity\User;

class AddMultipleNextToQueueEvent extends AddMultipleToQueueEvent
{
    /**
     * @param Music[] $added
     */
    public function __construct(
        User $user,
        array $added,
        int $currentPosition,
    ) {
        parent::__construct($user, $added, $currentPosition);
    }
}
