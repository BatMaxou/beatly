<?php

namespace App\Security\Voter;

use App\Entity\Artist;
use App\Entity\Platform;
use App\Entity\User;
use App\Enum\VoterRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class RoleVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            VoterRoleEnum::ADMIN->value,
            VoterRoleEnum::ARTIST->value,
            VoterRoleEnum::RAW_USER->value,
            VoterRoleEnum::UNBANED->value,
        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            VoterRoleEnum::UNBANED->value => !$user->isBanned(),
            VoterRoleEnum::RAW_USER->value => !$user->isBanned() && !$user->isPlatform() && !$user->isArtist(),
            VoterRoleEnum::ARTIST->value => $user instanceof Artist && $user->isArtist() && !$user->isBanned(),
            VoterRoleEnum::ADMIN->value => $user instanceof Platform && $user->isPlatform() && !$user->isBanned(),
            default => throw new \LogicException(sprintf('Unknown attribute %s', $attribute)),
        };
    }
}
