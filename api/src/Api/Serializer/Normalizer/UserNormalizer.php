<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class UserNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'user_normalizer_already_called';

    public function __construct(
        private readonly string $publicUploadsPath,
    ) {
    }

    /**
     * @param User $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string
    {
        $normalized = $this->normalizer->normalize(
            $object,
            $format,
            $context + [self::ALREADY_CALLED => true]
        );

        if (isset($normalized['avatarName'])) {
            unset($normalized['avatarName']);
            $normalized['avatar'] = \sprintf(
                '%s/users/avatars/%s',
                $this->publicUploadsPath,
                $object->getAvatarName(),
            );
        }

        if (isset($normalized['wallpaperName'])) {
            unset($normalized['wallpaperName']);
            $normalized['wallpaper'] = \sprintf(
                '%s/users/wallpapers/%s',
                $this->publicUploadsPath,
                $object->getWallpaperName(),
            );
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return ($data instanceof User || is_subclass_of($data, User::class)) && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            User::class => false,
        ];
    }
}
