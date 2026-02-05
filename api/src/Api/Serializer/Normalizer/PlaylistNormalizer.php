<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Playlist;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PlaylistNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'playlist_normalizer_already_called';

    public function __construct(
        private readonly string $publicUploadsPath,
    ) {
    }

    /**
     * @param Playlist $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string
    {
        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );

        if (isset($normalized['musics']) && is_array($normalized['musics']) && isset($normalized['musics'][0]['addedAt'])) {
            usort($normalized['musics'], function ($a, $b) {
                return $a['addedAt'] <=> $b['addedAt'];
            });
        }

        if (isset($normalized['coverName'])) {
            unset($normalized['coverName']);
            $normalized['cover'] = sprintf(
                '%s/playlists/covers/%s',
                $this->publicUploadsPath,
                $object->getCoverName(),
            );
        }

        if (isset($normalized['wallpaperName'])) {
            unset($normalized['wallpaperName']);
            $normalized['wallpaper'] = sprintf(
                '%s/playlists/wallpapers/%s',
                $this->publicUploadsPath,
                $object->getWallpaperName(),
            );
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Playlist && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Playlist::class => false,
        ];
    }
}
