<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Interface\LikableEntityInterface;
use App\Enum\ApiReusableRoute;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LikableNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'likable_normalizer_already_called';

    public function __construct(
        private readonly Security $security,
        private readonly FavoriteRepository $favoriteRepository,
    ) {
    }

    /**
     * @param LikableEntityInterface $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string
    {
        $alreadyCalledKey = \sprintf('%s.%s', self::ALREADY_CALLED, $object::class);

        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [$alreadyCalledKey => true]
        );

        if (in_array($context['operation_name'] ?? null, [
            ApiReusableRoute::GET_FAVORITES->value,
            ApiReusableRoute::GET_FAVORITE_MUSICS->value,
            ApiReusableRoute::GET_FAVORITE_ALBUMS->value,
            ApiReusableRoute::GET_FAVORITE_PLAYLISTS->value,
        ])) {
            return $normalized;
        }

        $user = $this->security->getUser();
        $normalized['isFavorite'] = null !== $this->favoriteRepository->findFor($object, $user);

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return in_array(LikableEntityInterface::class, class_implements($data))
            && false === ($context[\sprintf('%s.%s', self::ALREADY_CALLED, $data::class)] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            LikableEntityInterface::class => false,
        ];
    }
}
