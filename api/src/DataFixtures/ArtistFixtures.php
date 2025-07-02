<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\DataFixtures\Sample\SampleLoader;
use App\DataFixtures\Sample\SampleType;
use App\Entity\Artist;
use App\Entity\Album;
use App\Entity\Music;
use App\Entity\MusicFile;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        private readonly SampleLoader $sampleLoader
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
            $category = (new Category())
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
        $artist = (new Artist())
            ->setName($name)
            ->setEmail(strtolower(str_replace(' ', '.', $name)) . '@music.com')
            ->setPassword('azerty', true);
        
        if ($this->faker->boolean(80)) {
            // TODO: Uncomment after implementing uploader on User entity
            // $artist->setAvatar($this->createCover());
        }
        
        if ($this->faker->boolean(70)) {
            // TODO: Uncomment after implementing uploader on User entity
            // $artist->setWallpaper($this->createWallpaper());
        }

        $this->manager->persist($artist);

        return $artist;
    }

    private function createAlbumsAndMusics(array $artists): void
    {
        $musicFileCounter = 1;
        foreach ($artists as $artistName => $artistInfo) {
            $artist = $artistInfo['entity'];
            $artistData = $artistInfo['data'];
            
            foreach ($artistData['albums'] as $albumTitle => $songs) {
                $album = (new Album())
                    ->setTitle($albumTitle)
                    ->setReleaseDate($this->faker->dateTimeBetween('-15 years', 'now'));

                if ($this->faker->boolean(90)) {
                    // TODO: Uncomment after implementing uploader on Album entity
                    // $album->setCover($this->createCover());
                }

                if ($this->faker->boolean(60)) {
                    // TODO: Uncomment after implementing uploader on Album entity
                    // $album->setWallpaper($this->createWallpaper());
                }

                $this->manager->persist($album);

                foreach ($songs as $songTitle => $songCategories) {
                    $musicFile = (new MusicFile())->setName($this->createMusicFile());
                    $this->manager->persist($musicFile);

                    $music = (new Music())
                        ->setTitle($songTitle)
                        ->setCoverName($this->createCover())
                        ->setFile($musicFile);

                    $music->addArtist($artist);
                    $music->addAlbum($album);
                    foreach ($songCategories as $category) {
                        if (isset($this->categories[$category->value])) {
                            $music->addCategory($this->categories[$category->value]);
                        }
                    }

                    $this->manager->persist($music);
                    $musicFileCounter++;
                }
            }
        }
    }
    private function createMusicFile(): string
    {
        $name = sprintf('track_%s.mp3', $this->faker->unique()->slug(3));
        $rootDir = sprintf('%s/../..', __DIR__);

        copy(sprintf('%s/track.mp3', __DIR__), sprintf('%s%s/musics/%s', $rootDir, $this->privateUploadsPath, $name));

        return $name;
    }

    private function createCover(): string
    {
        $name = sprintf('cover_%s.jpg', $this->faker->unique()->slug(3));
        $rootDir = sprintf('%s/../..', __DIR__);

        copy(sprintf('%s/300x300.jpg', __DIR__), sprintf('%s%s/musics/covers/%s', $rootDir, $this->ssrPublicUploadsPath, $name));

        return $name;
    }

    private function createWallpaper(): string
    {
        $name = sprintf('wallpaper_%s.jpg', $this->faker->unique()->slug(3));
        $rootDir = sprintf('%s/../..', __DIR__);

        copy(sprintf('%s/1200x400.jpg', __DIR__), sprintf('%s%s/musics/wallpapers/%s', $rootDir, $this->ssrPublicUploadsPath, $name));

        return $name;
    }
}
