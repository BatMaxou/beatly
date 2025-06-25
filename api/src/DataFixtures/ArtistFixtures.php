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
use App\Entity\User;
use App\Entity\Playlist;
use App\Enum\RoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class ArtistFixtures extends Fixture
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    /** @var Category[] */
    private array $categories;
    private readonly ObjectManager $manager;

    public function __construct(
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
            $artist->setAvatar('https://via.placeholder.com/300x300/'. $this->faker->hexColor .'/ffffff?text=' . urlencode(substr($name, 0, 2)));
        }
        
        if ($this->faker->boolean(70)) {
            $artist->setWallpaper('https://via.placeholder.com/1200x400/'. $this->faker->hexColor .'/ffffff?text=' . urlencode($name));
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
                    $album->setCover('https://via.placeholder.com/400x400/'. $this->faker->hexColor .'/ffffff?text=Album');
                }

                if ($this->faker->boolean(60)) {
                    $album->setWallpaper('https://via.placeholder.com/1200x400/'. $this->faker->hexColor .'/ffffff?text=Album');
                }

                $this->manager->persist($album);

                foreach ($songs as $songTitle => $songCategories) {
                    $name = sprintf('track_%d.mp3', $musicFileCounter);
                    $musicFile = (new MusicFile())
                        ->setName($name)
                        ->setFile(new File($name, false));
                    $this->manager->persist($musicFile);

                    $music = (new Music())
                        ->setTitle($songTitle)
                        ->setDuration(20)
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
}
