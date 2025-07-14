<?php

namespace App\Event\Event\Interface;

use App\Entity\User;

interface UserEvent
{
    public function getUser(): User;
}
