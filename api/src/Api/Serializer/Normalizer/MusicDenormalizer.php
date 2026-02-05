<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Music;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MusicDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'music_denormalizer_already_called';

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Music
    {
        if (isset($data['albumPosition']) && is_string($data['albumPosition'])) {
            $data['albumPosition'] = (int) $data['albumPosition'];
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
        return Music::class === $type && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Music::class => false,
        ];
    }
}
