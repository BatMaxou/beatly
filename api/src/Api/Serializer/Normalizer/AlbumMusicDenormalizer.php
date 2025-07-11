<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\AlbumMusic;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class AlbumMusicDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'album_music_denormalizer_already_called';

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): AlbumMusic
    {
        if (isset($data['position']) && is_string($data['position'])) {
            $data['position'] = (int) $data['position'];
        }

        return $this->denormalizer->denormalize(
            $data,
            $type,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $type === AlbumMusic::class && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            AlbumMusic::class => false,
        ];
    }
}
