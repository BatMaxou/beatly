<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\MusicFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MusicFileNormalizer implements NormalizerInterface, NormalizerAwareInterface 
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'music_file_normalizer_already_called';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );

        $normalized['contentUrl'] = sprintf(
            '%s/%d',
            $this->urlGenerator->generate('api_upload_music_file', [], UrlGeneratorInterface::ABSOLUTE_PATH),
            $object->getId(),
        );

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof MusicFile && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            MusicFile::class => false,
        ];
    }
}
