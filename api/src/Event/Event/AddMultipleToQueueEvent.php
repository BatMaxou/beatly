<?php

namespace App\Event\Event;

use App\Entity\Music;
use App\Entity\User;

class AddMultipleToQueueEvent extends AbstractUserAwareEvent
{
    /**
     * @param Music[] $added
     */
    public function __construct(
        User $user,
        public array $added,
        public ?int $currentPosition = null,
    ) {
        parent::__construct($user);
    }
}
