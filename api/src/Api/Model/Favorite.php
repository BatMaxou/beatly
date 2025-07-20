<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Api\Provider\FavoriteProvider;
use App\Domain\Command\AddFavoriteCommand;
use App\Domain\Command\RemoveFavoriteCommand;
use App\Enum\ApiReusableRoute;
use App\Enum\VoterRoleEnum;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/favorites/add',
            name: 'api_add_favorite',
            messenger: 'input',
            input: AddFavoriteCommand::class,
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Post(
            uriTemplate: '/favorites/remove',
            name: 'api_remove_favorite',
            messenger: 'input',
            input: RemoveFavoriteCommand::class,
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Get(
            uriTemplate: '/favorites',
            provider: FavoriteProvider::class,
            name: ApiReusableRoute::GET_FAVORITES->value,
            normalizationContext: ['groups' => ['favorite:read', 'favorite:all:read']],
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Get(
            uriTemplate: '/favorites/musics',
            provider: FavoriteProvider::class,
            name: ApiReusableRoute::GET_FAVORITE_MUSICS->value,
            normalizationContext: ['groups' => ['favorite:read', 'favorite:musics:read']],
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Get(
            uriTemplate: '/favorites/albums',
            provider: FavoriteProvider::class,
            name: ApiReusableRoute::GET_FAVORITE_ALBUMS->value,
            normalizationContext: ['groups' => ['favorite:read', 'favorite:albums:read']],
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Get(
            uriTemplate: '/favorites/playlists',
            provider: FavoriteProvider::class,
            name: ApiReusableRoute::GET_FAVORITE_PLAYLISTS->value,
            normalizationContext: ['groups' => ['favorite:read', 'favorite:playlists:read']],
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
    ],
)]
class Favorite
{
    public function __construct(
        public array $musics = [],
        public array $albums = [],
        public array $playlists = [],
    ) {
    }
}
