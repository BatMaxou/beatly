<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Queue;
use App\Entity\RandomQueue;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class QueueNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'queue_normalizer_already_called';

    /**
     * @param Queue $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string
    {
        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );

        if (isset($normalized['queueItems']) && is_array($normalized['queueItems'])) {
            usort($normalized['queueItems'], function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return ($data instanceof Queue || $data instanceof RandomQueue) && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Queue::class => false,
            RandomQueue::class => false,
        ];
    }
}
