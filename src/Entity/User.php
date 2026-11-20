<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cette adresse email est déjà utilisée par un autre compte.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    #[Assert\Length(max: 180, maxMessage: "L'adresse email ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastConnectionAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Notice>
     */
    #[ORM\OneToMany(targetEntity: Notice::class, mappedBy: 'updatedBy')]
    private Collection $notices;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'updatedBy')]
    private Collection $messages;

    /**
     * @var Collection<int, RoomCategory>
     */
    #[ORM\OneToMany(targetEntity: RoomCategory::class, mappedBy: 'createdBy')]
    private Collection $roomCategoriesCreatedBy;

    /**
     * @var Collection<int, RoomCategory>
     */
    #[ORM\OneToMany(targetEntity: RoomCategory::class, mappedBy: 'updatedBy')]
    private Collection $roomCategoriesUpdatedBy;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.", normalizer: 'trim')]
    #[Assert\Length(max: 50, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $name = null;


    public function __construct()
    {
        $this->notices = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->roomCategoriesCreatedBy = new ArrayCollection();
        $this->roomCategoriesUpdatedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLastConnectionAt(): ?\DateTimeImmutable
    {
        return $this->lastConnectionAt;
    }

    public function setLastConnectionAt(?\DateTimeImmutable $lastConnectionAt): static
    {
        $this->lastConnectionAt = $lastConnectionAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Notice>
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function addNotice(Notice $notice): static
    {
        if (!$this->notices->contains($notice)) {
            $this->notices->add($notice);
            $notice->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeNotice(Notice $notice): static
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getUpdatedBy() === $this) {
                $notice->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUpdatedBy() === $this) {
                $message->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RoomCategory>
     */
    public function getRoomCategoriesCreatedBy(): Collection
    {
        return $this->roomCategoriesCreatedBy;
    }

    public function addRoomCategoriesCreatedBy(RoomCategory $roomCategoriesCreatedBy): static
    {
        if (!$this->roomCategoriesCreatedBy->contains($roomCategoriesCreatedBy)) {
            $this->roomCategoriesCreatedBy->add($roomCategoriesCreatedBy);
            $roomCategoriesCreatedBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeRoomCategoriesCreatedBy(RoomCategory $roomCategoriesCreatedBy): static
    {
        if ($this->roomCategoriesCreatedBy->removeElement($roomCategoriesCreatedBy)) {
            // set the owning side to null (unless already changed)
            if ($roomCategoriesCreatedBy->getCreatedBy() === $this) {
                $roomCategoriesCreatedBy->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RoomCategory>
     */
    public function getRoomCategoriesUpdatedBy(): Collection
    {
        return $this->roomCategoriesUpdatedBy;
    }

    public function addRoomCategoriesUpdatedBy(RoomCategory $roomCategoriesUpdatedBy): static
    {
        if (!$this->roomCategoriesUpdatedBy->contains($roomCategoriesUpdatedBy)) {
            $this->roomCategoriesUpdatedBy->add($roomCategoriesUpdatedBy);
            $roomCategoriesUpdatedBy->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeRoomCategoriesUpdatedBy(RoomCategory $roomCategoriesUpdatedBy): static
    {
        if ($this->roomCategoriesUpdatedBy->removeElement($roomCategoriesUpdatedBy)) {
            // set the owning side to null (unless already changed)
            if ($roomCategoriesUpdatedBy->getUpdatedBy() === $this) {
                $roomCategoriesUpdatedBy->setUpdatedBy(null);
            }
        }

        return $this;
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
}
