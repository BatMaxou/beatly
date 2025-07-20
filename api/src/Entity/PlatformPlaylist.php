<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\PlaylistCreationProcessor;
use App\Enum\ApiReusableRoute;
use App\Enum\EmbeddingEnum;
use App\Enum\RoleEnum;
use App\Enum\VoterRoleEnum;
use App\Repository\PlatformPlaylistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformPlaylistRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: ApiReusableRoute::CREATE_PLAYLIST->value,
            processor: PlaylistCreationProcessor::class,
            normalizationContext: ['groups' => ['playlist:read']],
            denormalizationContext: ['groups' => ['playlist:write']],
            security: 'is_granted("'.VoterRoleEnum::ADMIN->value.'")',
        ),
    ],
)]
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
