<?php

namespace App\Domain\Command;

class VerifyResetTokenCommand
{
    public function __construct(
        public string $token,
    ) {
    }
}
