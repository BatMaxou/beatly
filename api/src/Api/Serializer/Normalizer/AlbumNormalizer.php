<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Album;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AlbumNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'album_normalizer_already_called';

    public function __construct(
        private readonly string $publicUploadsPath,
    ) {
    }

    /**
     * @param Album $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string
    {
        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );

        if (isset($normalized['musics']) && is_array($normalized['musics'])) {
            usort($normalized['musics'], function ($a, $b) {
                return $a['albumPosition'] <=> $b['albumPosition'];
            });
        }

        if (isset($normalized['coverName'])) {
            unset($normalized['coverName']);
            $normalized['cover'] = sprintf(
                '%s/albums/covers/%s',
                $this->publicUploadsPath,
                $object->getCoverName(),
            );
        }

        if (isset($normalized['wallpaperName'])) {
            unset($normalized['wallpaperName']);
            $normalized['wallpaper'] = sprintf(
                '%s/albums/wallpapers/%s',
                $this->publicUploadsPath,
                $object->getWallpaperName(),
            );
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Album && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Album::class => false,
        ];
    }
}
