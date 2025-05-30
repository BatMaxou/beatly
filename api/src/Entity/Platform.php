<?php

namespace App\Entity;

use App\Repository\PlatformRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
class Platform extends User
{
}
