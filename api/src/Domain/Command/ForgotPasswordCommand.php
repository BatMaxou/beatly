<?php

namespace App\Domain\Command;

class ForgotPasswordCommand
{
    public function __construct(
        public string $email,
    ) {
    }
}
