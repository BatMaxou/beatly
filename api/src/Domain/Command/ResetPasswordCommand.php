<?php

namespace App\Domain\Command;

class ResetPasswordCommand
{
    public function __construct(
        public string $token,
        public string $password,
    ) {
    }
}
