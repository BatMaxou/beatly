<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\RoleEnum;
use App\Repository\PlatformPlaylistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformPlaylistRepository::class)]
#[ApiResource]
class PlatformPlaylist extends Playlist
{
    public function setCreator(?User $creator): static
    {
        if ($creator && !$creator->hasRole(RoleEnum::PLATFORM)) {
            throw new \InvalidArgumentException('Creator must be a platform user.');
        }

        return parent::setCreator($creator);
    }
}
