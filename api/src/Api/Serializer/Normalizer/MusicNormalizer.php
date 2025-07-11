<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Music;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MusicNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'music_normalizer_already_called';

    public function __construct(
        private readonly string $publicUploadsPath,
    ) {
    }

    /**
     * @param Music $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string
    {
        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );

        if (!isset($normalized['coverName'])) {
            return $normalized;
        }

        unset($normalized['coverName']);
        $normalized['cover'] = sprintf(
            '%s/musics/covers/%s',
            $this->publicUploadsPath,
            $object->getCoverName(),
        );

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Music && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Music::class => false,
        ];
    }
}
