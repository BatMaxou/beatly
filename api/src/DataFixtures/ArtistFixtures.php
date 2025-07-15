<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\DataFixtures\Sample\SampleLoader;
use App\DataFixtures\Sample\SampleType;
use App\Entity\Album;
use App\Entity\AlbumMusic;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Music;
use App\Entity\MusicFile;
use App\Service\Mp3MetadataManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Vich\UploaderBundle\Storage\StorageInterface;

class ArtistFixtures extends Fixture
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    /** @var Category[] */
    private array $categories;
    private readonly ObjectManager $manager;

    public function __construct(
        private readonly string $ssrPublicUploadsPath,
        private readonly string $privateUploadsPath,
        private readonly SampleLoader $sampleLoader,
        private readonly StorageInterface $storage,
        private readonly Mp3MetadataManager $mp3MetadataManager,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->categories = $this->createCategories();
        $artists = $this->createArtists();
        $this->createAlbumsAndMusics($artists);

        $manager->flush();
    }

    private function createCategories(): array
    {
        $categories = [];
        foreach ($this->sampleLoader->load(SampleType::CATEGORY) as $categoryData) {
            $category = new Category()
                ->setName($categoryData['name'])
                ->setColor($categoryData['color']);

            $this->manager->persist($category);
            $categories[$categoryData['name']] = $category;
        }

        return $categories;
    }

    private function createArtists(): array
    {
        $artists = [];

        foreach ($this->sampleLoader->load(SampleType::ARTIST) as $artistData) {
            $artistName = $artistData['name'];
            $artist = $this->createArtist($artistName);
            $artists[$artistName] = ['entity' => $artist, 'data' => $artistData];
        }

        return $artists;
    }

    private function createArtist(string $name): Artist
    {
        $artist = new Artist()
            ->setName($name)
            ->setEmail(strtolower(str_replace(' ', '.', $name)).'@music.com')
            ->setPassword('azerty');

        if ($this->faker->boolean(10)) {
            $artist->setAvatarName($this->createAvatar());
        }

        if ($this->faker->boolean(10)) {
            $artist->setWallpaperName($this->createWallpaper('user'));
        }

        $this->manager->persist($artist);

        return $artist;
    }

    private function createAlbumsAndMusics(array $artists): void
    {
        foreach ($artists as $artistName => $artistInfo) {
            $artist = $artistInfo['entity'];
            $artistData = $artistInfo['data'];
            foreach ($artistData['albums'] as $albumTitle => $songs) {
                $album = new Album()
                    ->setTitle($albumTitle)
                    ->setArtist($artist)
                    ->setReleaseDate($this->faker->dateTimeBetween('-15 years', 'now'));

                if ($this->faker->boolean(10)) {
                    $album->setCoverName($this->createCover('album'));
                }

                if ($this->faker->boolean(10)) {
                    $album->setWallpaperName($this->createWallpaper('album'));
                }

                $this->manager->persist($album);

                $musicPosition = 1;
                foreach ($songs as $songTitle => $songCategories) {
                    $musicFile = new MusicFile()->setName($this->createMusicFile());
                    $this->manager->persist($musicFile);

                    $filePath = $this->storage->resolvePath($musicFile, 'file');
                    $music = new Music()
                        ->setTitle($songTitle)
                        ->setFile($musicFile)
                        ->setListeningsNumber($this->faker->numberBetween(50000, 1000000))
                        ->setDuration($this->mp3MetadataManager->getFor($filePath)['duration']);

                    if ($this->faker->boolean(10)) {
                        $music->setCoverName($this->createCover('music'));
                    }

                    $music->setMainArtist($artist);
                    $music->addAlbum(
                        new AlbumMusic()
                        ->setAlbum($album)
                        ->setPosition($musicPosition)
                    );

                    foreach ($songCategories as $category) {
                        if (isset($this->categories[$category->value])) {
                            $music->addCategory($this->categories[$category->value]);
                        }
                    }

                    $this->manager->persist($music);
                    ++$musicPosition;
                }
            }
        }
    }

    private function createMusicFile(): string
    {
        $name = \sprintf('track_%s.mp3', $this->faker->unique()->slug(3));
        $rootDir = \sprintf('%s/../..', __DIR__);

        copy(\sprintf('%s/track.mp3', __DIR__), \sprintf('%s%s/musics/%s', $rootDir, $this->privateUploadsPath, $name));

        return $name;
    }

    private function createAvatar(): string
    {
        $name = \sprintf('avatar_%s.jpg', $this->faker->unique()->slug(3));
        $rootDir = \sprintf('%s/../..', __DIR__);

        copy(\sprintf('%s/300x300.jpg', __DIR__), \sprintf('%s%s/users/avatars/%s', $rootDir, $this->ssrPublicUploadsPath, $name));

        return $name;
    }

    private function createCover(string $type): string
    {
        $name = \sprintf('cover_%s.jpg', $this->faker->unique()->slug(3));
        $rootDir = \sprintf('%s/../..', __DIR__);

        copy(\sprintf('%s/300x300.jpg', __DIR__), \sprintf('%s%s/%ss/covers/%s', $rootDir, $this->ssrPublicUploadsPath, $type, $name));

        return $name;
    }

    private function createWallpaper(string $type): string
    {
        $name = \sprintf('wallpaper_%s.jpg', $this->faker->unique()->slug(3));
        $rootDir = \sprintf('%s/../..', __DIR__);

        copy(\sprintf('%s/1200x400.jpg', __DIR__), \sprintf('%s%s/%ss/wallpapers/%s', $rootDir, $this->ssrPublicUploadsPath, $type, $name));

        return $name;
    }
}
