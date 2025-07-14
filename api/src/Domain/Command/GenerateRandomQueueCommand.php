<?php

namespace App\Domain\Command;

class GenerateRandomQueueCommand
{
    public function __construct(
        public readonly int $currentPosition,
    ) {
    }
}
