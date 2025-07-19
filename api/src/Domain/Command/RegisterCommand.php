<?php

namespace App\Domain\Command;

class RegisterCommand
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
    ) {
    }
}
