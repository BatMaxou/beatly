<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\MusicFileController;
use App\Enum\VoterRoleEnum;
use App\Repository\MusicFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MusicFileRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: 'api_upload_music_file',
            normalizationContext: ['groups' => ['music_file:read']],
            denormalizationContext: ['groups' => ['music_file:upload']],
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Get(
            name: 'api_get_music_file',
            controller: MusicFileController::class,
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Delete(
            name: 'api_delete_music_file',
            security: '
                is_granted("'.VoterRoleEnum::ADMIN->value.'")
                or (is_granted("'.VoterRoleEnum::ARTIST->value.'") and is_granted("'.VoterRoleEnum::OWNER->value.'", object))',
        ),
    ]
)]
class MusicFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Vich\UploadableField(mapping: 'music_file', fileNameProperty: 'name')]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    private ?ArtistRequest $artistRequest = null;

    #[ORM\OneToOne(mappedBy: 'file', cascade: ['persist', 'remove'])]
    private ?Music $music = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file = null): static
    {
        $this->file = $file;

        if ($file) {
            // Important to update at least one field to trigger the doctrine events
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getArtistRequest(): ?ArtistRequest
    {
        return $this->artistRequest;
    }

    public function setArtistRequest(?ArtistRequest $artistRequest): static
    {
        $this->artistRequest = $artistRequest;

        return $this;
    }

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function setMusic(?Music $music): static
    {
        $this->music = $music;

        if ($music) {
            $music->setFile($this);
        }

        return $this;
    }
}
