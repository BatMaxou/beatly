<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\EmbeddingEnum;
use App\Enum\RoleEnum;
use App\Repository\PlatformPlaylistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformPlaylistRepository::class)]
#[ApiResource]
class PlatformPlaylist extends Playlist
{
    public function setCreator(?User $creator): static
    {
        if ($creator && !$creator->hasRole(RoleEnum::PLATFORM) && !$creator instanceof Platform) {
            throw new \InvalidArgumentException('Creator must be a platform user.');
        }

        return parent::setCreator($creator);
    }

    public function prepareForEmbedding(EmbeddingEnum $type): string
    {
        return match ($type) {
            EmbeddingEnum::SEARCH => sprintf('%s - %s', $this->getTitle(), 'Beatly'),
            default => throw new \InvalidArgumentException(sprintf('Unsupported embedding type: %s', $type->value)),
        };
    }
}
