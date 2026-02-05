<?php

namespace App\Entity;

use App\Enum\RoleEnum;
use App\Repository\PlatformRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
class Platform extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->addRole(RoleEnum::PLATFORM);
    }
}
