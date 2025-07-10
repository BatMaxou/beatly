<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Playlist;
use App\Entity\PlaylistMusic;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\MusicRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    protected ObjectManager $manager;

    private const PLAYLIST_NAMES = [
        'Today\'s Top Hits', 'Pop Rising', 'RapCaviar', 'Rock Classics', 'Chill Hits',
        'Your Summer Rewind', 'Mood Booster', 'Acoustic Covers', 'Feel Good Friday',
        'Workout Hits', 'Study Playlist', 'Road Trip Songs', 'Late Night Vibes',
        'Throwback Thursday', 'New Music Friday', 'Hip Hop Central', 'Pop Punk Powerhouse',
        'R&B Soul', 'Electronic Dance', 'Indie Pop', 'Country Hits', 'Jazz Classics',
        'Alternative Rock', 'Reggae Roots', 'Latin Hits', 'K-Pop Rising', 'Oldies but Goodies',
        'Songs to Sing in the Car', 'Rainy Day Playlist', 'Happy Hits', 'Sad Songs',
        'Party Mix', 'Love Songs', 'Breakup Playlist', 'Motivation Station',
        'Deep Focus', 'Sleep Sounds', 'Morning Motivation', 'Evening Chill'
    ];
    /** @var Music[] */
    private array $musics;

    public function __construct(
        private readonly MusicRepository $musicRepository,
    ) {
        $this->initializeFaker();
    }

    public function getDependencies(): array
    {
        return [
            ArtistFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->initializeMusics();

        $this->createUser('test@gmail.com', 'Test', 'User');
        $this->createUsers(20);
        $this->createBannedUsers(5);

        $manager->flush();
    }

    protected function initializeMusics(): void
    {
        $this->musics = $this->musicRepository->findAll();
    }

    protected function createUsers(int $count, ?callable $process = null): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $user = $process ? $process() : $this->createUser();

            if (!$user instanceof User) {
                throw new \LogicException('Entity User not found');
            }

            $this->manager->persist($user);
            $this->createPlaylists($user);
        }
    }

    protected function createUser(?string $email = null, ?string $name = null): User
    {
        return (new User())
            ->setEmail($email ?? $this->faker->email())
            ->setName($name ?? $this->faker->name())
            ->setPassword('azerty');
    }

    protected function createPlaylists(User $user, ?callable $process = null): User
    {
        for ($i = 0; $i < $this->faker->numberBetween(0, 5); ++$i) {
            $playlist = $process ? $process($user) : $this->createPlaylist($user);

            if (!$playlist instanceof Playlist) {
                throw new \LogicException('Entity Playlist not found');
            }
            
            $this->manager->persist($playlist);
            $user->addPlaylist($playlist);
        }

        return $user;
    }

    protected function createPlaylist(User $user): Playlist
    {
        return $this->hydratePlaylist(new Playlist())
            ->setTitle($this->faker->randomElement(self::PLAYLIST_NAMES))
            ->setCreator($user);
    }

    protected function hydratePlaylist(Playlist $playlist): Playlist
    {
        for ($j = 0; $j < $this->faker->numberBetween(5, 15); ++$j) {
            $music = $this->faker->randomElement($this->musics);
            $playlist->addMusic((new PlaylistMusic()
                ->setMusic($music)
                ->setPosition($j + 1)
            ));
        }

        return $playlist;
    }

    private function createBannedUsers(int $count): void
    {
        $this->createUsers($count, $this->createBannedUser(...));
    }


    private function createBannedUser(): User
    {
        $user = $this->createUser();
        $user->addRole(RoleEnum::BANNED);

        return $user;
    }
}
