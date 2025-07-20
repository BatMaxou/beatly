<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Processor\UserFilesProcessor;
use App\Api\Provider\MeProvider;
use App\Domain\Command\ForgotPasswordCommand;
use App\Domain\Command\RegisterCommand;
use App\Domain\Command\ResetPasswordCommand;
use App\Domain\Command\VerifyResetTokenCommand;
use App\Enum\ApiReusableRoute;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/register',
            name: 'api_register',
            messenger: 'input',
            input: RegisterCommand::class,
        ),
        new Post(
            uriTemplate: '/forgot-password',
            name: 'api_forgot_password',
            messenger: 'input',
            input: ForgotPasswordCommand::class,
        ),
        new Post(
            uriTemplate: '/reset-password',
            name: 'api_reset_password',
            messenger: 'input',
            input: ResetPasswordCommand::class,
        ),
        new Post(
            uriTemplate: '/verify-token',
            name: 'api_verify_token',
            messenger: 'input',
            input: VerifyResetTokenCommand::class,
        ),
        new Post(
            name: ApiReusableRoute::UPDATE_USER_FILES->value,
            uriTemplate: '/users/{id}/files',
            processor: UserFilesProcessor::class,
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Patch(
            name: 'api_update_user',
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['user:update']],
        ),
        new Get(
            name: 'api_get_user',
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Get(
            uriTemplate: '/me',
            provider: MeProvider::class,
            name: ApiReusableRoute::ME->value,
            normalizationContext: ['groups' => ['user:read']]
        ),
        new GetCollection(
            name: 'api_get_user_collection',
            normalizationContext: ['groups' => ['user:collection:read']],
        ),
        new Delete(
            name: 'api_delete_playlist',
        ),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'user' => User::class,
    'platform' => Platform::class,
    'artist' => Artist::class,
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarName = null;

    #[Vich\UploadableField(mapping: 'user_avatar', fileNameProperty: 'avatarName')]
    private ?File $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wallpaperName = null;

    #[Vich\UploadableField(mapping: 'user_wallpaper', fileNameProperty: 'wallpaperName')]
    private ?File $wallpaper = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Playlist>
     */
    #[ORM\OneToMany(targetEntity: Playlist::class, mappedBy: 'creator', cascade: ['persist', 'remove'])]
    private Collection $playlists;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Queue $queue = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?RandomQueue $randomQueue = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?ArtistRequest $artistRequest = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->addRole(RoleEnum::USER);
        $this->playlists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): static
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    public function setAvatar(?File $avatar = null): static
    {
        $this->avatar = $avatar;

        if ($avatar) {
            // Important to update at least one field to trigger the doctrine events
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getWallpaperName(): ?string
    {
        return $this->wallpaperName;
    }

    public function setWallpaperName(?string $wallpaperName): static
    {
        $this->wallpaperName = $wallpaperName;

        return $this;
    }

    public function getWallpaper(): ?File
    {
        return $this->wallpaper;
    }

    public function setWallpaper(?File $wallpaper = null): static
    {
        $this->wallpaper = $wallpaper;

        if ($wallpaper) {
            // Important to update at least one field to trigger the doctrine events
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password, bool $alreadyHashed = false): static
    {
        if ($alreadyHashed) {
            $this->password = $password;
        } else {
            $this->plainPassword = $password;
            $this->password = null;
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(RoleEnum $role): static
    {
        if (!in_array($role->value, $this->roles, true)) {
            $this->roles[] = $role->value;
        }

        return $this;
    }

    public function removeRole(RoleEnum $role): static
    {
        if (in_array($role->value, $this->roles, true)) {
            $this->roles = array_diff($this->roles, [$role->value]);
        }

        return $this;
    }

    public function hasRole(RoleEnum $role): bool
    {
        return in_array($role->value, $this->roles, true);
    }

    public function isBanned(): bool
    {
        return $this->hasRole(RoleEnum::BANNED);
    }

    public function isPlatform(): bool
    {
        return $this->hasRole(RoleEnum::PLATFORM);
    }

    public function getUserIdentifier(): string
    {
        if (null === $this->email) {
            throw new \LogicException('The User class must implement the UserInterface, but none of the required methods can be called.');
        }

        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): static
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists->add($playlist);
            $playlist->setCreator($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): static
    {
        if ($this->playlists->removeElement($playlist)) {
            // set the owning side to null (unless already changed)
            if ($playlist->getCreator() === $this) {
                $playlist->setCreator(null);
            }
        }

        return $this;
    }

    public function getQueue(): ?Queue
    {
        return $this->queue;
    }

    public function setQueue(Queue $queue): static
    {
        // set the owning side of the relation if necessary
        if ($queue->getUser() !== $this) {
            $queue->setUser($this);
        }

        $this->queue = $queue;

        return $this;
    }

    public function getRandomQueue(): ?RandomQueue
    {
        return $this->randomQueue;
    }

    public function setRandomQueue(RandomQueue $randomQueue): static
    {
        // set the owning side of the relation if necessary
        if ($randomQueue->getUser() !== $this) {
            $randomQueue->setUser($this);
        }

        $this->randomQueue = $randomQueue;

        return $this;
    }

    public function getArtistRequest(): ?ArtistRequest
    {
        return $this->artistRequest;
    }

    public function setArtistRequest(ArtistRequest $artistRequest): static
    {
        // set the owning side of the relation if necessary
        if ($artistRequest->getUser() !== $this) {
            $artistRequest->setUser($this);
        }

        $this->artistRequest = $artistRequest;

        return $this;
    }
}
