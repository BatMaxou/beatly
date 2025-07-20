<?php

namespace App\Security\Voter;

use App\Entity\Album;
use App\Entity\ArtistRequest;
use App\Entity\Music;
use App\Entity\MusicFile;
use App\Entity\User;
use App\Enum\VoterRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class OwnVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            VoterRoleEnum::OWNER->value,
            VoterRoleEnum::SELF->value,
        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            VoterRoleEnum::SELF->value => $subject instanceof User && $user->getId() === $subject->getId(),
            VoterRoleEnum::OWNER->value => $this->isOwner($user, $subject),
            default => throw new \LogicException(sprintf('Unknown attribute %s', $attribute)),
        };
    }

    private function isOwner(User $user, mixed $subject): bool
    {
        return match (true) {
            $subject instanceof Album => $user->getId() === $subject->getArtist()?->getId(),
            $subject instanceof ArtistRequest => $user->getId() === $subject->getUser()?->getId(),
            $subject instanceof Music => $user->getId() === $subject->getMainArtist()?->getId(),
            $subject instanceof MusicFile => $user->getId() === $subject->getMusic()?->getMainArtist()?->getId()
                || $user->getId() === $subject->getArtistRequest()?->getUser()?->getId(),
            default => false,
        };
    }
}
