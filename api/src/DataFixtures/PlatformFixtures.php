<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\Persistence\ObjectManager;

class PlatformFixtures extends UserFixtures
{
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->initializeMusics();

        $this->createPlatformAdmins(3);

        $manager->flush();
    }

    private function createPlatformAdmins(int $count): void
    {
        $default = $this->createPlatformAdmin('beatly@gmail.com', 'Platform Admin');
        $this->createUsers($count - 1, $this->createPlatformAdmin(...));

        $this->manager->persist($default);
    }

    private function createPlatformAdmin(?string $email = null, ?string $username = null): User
    {
        $user = $this->createUser($email, $username);
        $user->addRole(RoleEnum::PLATFORM);

        return $user;
    }
}
