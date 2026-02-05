<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\ArtistRequestCreationProcessor;
use App\Domain\Command\AcceptArtistRequestCommand;
use App\Domain\Command\DeclineArtistRequestCommand;
use App\Enum\ApiReusableRoute;
use App\Enum\RequestStatusEnum;
use App\Enum\VoterRoleEnum;
use App\Repository\ArtistRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRequestRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            name: ApiReusableRoute::CREATE_ARTIST_REQUEST->value,
            processor: ArtistRequestCreationProcessor::class,
            denormalizationContext: ['groups' => ['request:write']],
            normalizationContext: ['groups' => ['request:read']],
            security: 'is_granted("'.VoterRoleEnum::RAW_USER->value.'") and is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Post(
            uriTemplate: '/artist_requests/accept',
            name: 'api_accept_artist_request',
            messenger: 'input',
            input: AcceptArtistRequestCommand::class,
            security: 'is_granted("'.VoterRoleEnum::ADMIN->value.'")'
        ),
        new Post(
            uriTemplate: '/artist_requests/decline',
            name: 'api_decline_artist_request',
            messenger: 'input',
            input: DeclineArtistRequestCommand::class,
            security: 'is_granted("'.VoterRoleEnum::ADMIN->value.'")'
        ),
        new Get(
            name: ApiReusableRoute::GET_MY_ARTIST_REQUEST->value,
            normalizationContext: ['groups' => ['request:read']],
            security: 'is_granted("'.VoterRoleEnum::OWNER->value.'", object) and is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
        new Get(
            name: 'api_get_artist_request',
            normalizationContext: ['groups' => ['request:read']],
            security: 'is_granted("'.VoterRoleEnum::ADMIN->value.'")'
        ),
        new GetCollection(
            name: 'api_get_artist_request_collection',
            normalizationContext: ['groups' => ['request:collection:read']],
            security: 'is_granted("'.VoterRoleEnum::ADMIN->value.'")'
        ),
    ]
)]
class ArtistRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'artistRequest')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    /**
     * @var Collection<int, MusicFile>
     */
    #[ORM\OneToMany(targetEntity: MusicFile::class, mappedBy: 'artistRequest', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $files;

    #[ORM\Column(enumType: RequestStatusEnum::class)]
    private ?RequestStatusEnum $status = null;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->status = RequestStatusEnum::PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection<int, MusicFile>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(MusicFile $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setArtistRequest($this);
        }

        return $this;
    }

    public function removeFile(MusicFile $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getArtistRequest() === $this) {
                $file->setArtistRequest(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?RequestStatusEnum
    {
        return $this->status;
    }

    public function setStatus(RequestStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }
}
