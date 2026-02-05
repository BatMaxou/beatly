<?php

namespace App\DataFixtures;

use App\Entity\Platform;
use App\Entity\PlatformPlaylist;
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

    private function createPlatformAdmin(?string $email = null, ?string $username = null): Platform
    {
        $user = $this->createUser($email, $username)
            ->addRole(RoleEnum::PLATFORM);

        $this->manager->persist($this->createPlatformPlaylist($user));

        return $user;
    }

    private function createUser(?string $email = null, ?string $name = null): Platform
    {
        return new Platform()
            ->setEmail($email ?? $this->faker->email())
            ->setName($name ?? $this->faker->name())
            ->setPassword('azerty');
    }

    private function createPlatformPlaylist(Platform $user): PlatformPlaylist
    {
        $playlist = $this->hydratePlaylist(new PlatformPlaylist())
            ->setTitle('Playlist Beatly')
            ->setCreator($user);

        if ($this->faker->boolean(10)) {
            $playlist->setCoverName($this->createCover());
        }

        if ($this->faker->boolean(10)) {
            $playlist->setWallpaperName($this->createWallpaper());
        }

        return $playlist;
    }
}
