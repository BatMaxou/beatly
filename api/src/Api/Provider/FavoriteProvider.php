<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Favorite;
use App\Enum\ApiReusableRoute;
use App\Repository\FavoriteAlbumRepository;
use App\Repository\FavoriteMusicRepository;
use App\Repository\FavoritePlaylistRepository;
use Symfony\Bundle\SecurityBundle\Security;

class FavoriteProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly FavoriteMusicRepository $favoriteMusicRepository,
        private readonly FavoriteAlbumRepository $favoriteAlbumRepository,
        private readonly FavoritePlaylistRepository $favoritePlaylistRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $isGlobal = ApiReusableRoute::GET_FAVORITES->value === $operation->getName();
        $isMusic = ApiReusableRoute::GET_FAVORITE_MUSICS->value === $operation->getName();
        $isAlbum = ApiReusableRoute::GET_FAVORITE_ALBUMS->value === $operation->getName();
        $isPlaylist = ApiReusableRoute::GET_FAVORITE_PLAYLISTS->value === $operation->getName();

        if (!$isGlobal && !$isMusic && !$isAlbum && !$isPlaylist) {
            throw new \LogicException(sprintf('Operation "%s" is not supported by %s', $operation->getName(), self::class));
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            throw new \LogicException('User must be authenticated to access the dashboard.');
        }

        return new Favorite(
            $isGlobal || $isMusic ? $this->favoriteMusicRepository->findByUser($currentUser) : [],
            $isGlobal || $isAlbum ? $this->favoriteAlbumRepository->findByUser($currentUser) : [],
            $isGlobal || $isPlaylist ? $this->favoritePlaylistRepository->findByUser($currentUser) : [],
        );
    }
}
