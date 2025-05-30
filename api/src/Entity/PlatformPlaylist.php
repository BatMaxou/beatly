<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PlatformPlaylistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformPlaylistRepository::class)]
#[ApiResource]
class PlatformPlaylist extends Playlist
{
}
