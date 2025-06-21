<?php

namespace App\Domain\Command;

class RegisterCommand
{
    public const USER_REGISTER = 'user_register';
    public const ARTIST_REGISTER = 'artist_register';

    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public string $registerType,
    ) {
    }
}
