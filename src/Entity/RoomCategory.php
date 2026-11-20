<?php

namespace App\Entity;

use App\Enum\RoomCategoryEnum;
use App\Repository\RoomCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomCategoryRepository::class)]
#[ORM\UniqueConstraint(
    name: 'UNIQ_IDENTIFIER_ROOM_CATEGORY_LABEL',
    fields: ['label']
)]
#[UniqueEntity(
    fields: ['label'],
    message: 'Cette catégorie de chambre est déjà utilisée.'
)]
class RoomCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: RoomCategoryEnum::class)]
    #[Assert\NotNull(message: "La catégorie de chambre est obligatoire.")]

    private ?RoomCategoryEnum $label = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix par nuit est obligatoire.")]
    #[Assert\Positive(message: 'Le prix doit être supérieur à zéro.')]
    #[Assert\LessThanOrEqual(
        value: 99999999.99,
        message: 'Le prix dépasse le montant maximal autorisé.'
    )]
    private ?string $pricePerNight = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'roomCategoriesCreatedBy')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'roomCategoriesUpdatedBy')]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, RoomCategoryPreference>
     */
    #[ORM\ManyToMany(targetEntity: RoomCategoryPreference::class, mappedBy: 'roomCategory')]
    private Collection $roomCategoryPreferences;

    public function __construct()
    {
        $this->roomCategoryPreferences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?RoomCategoryEnum
    {
        return $this->label;
    }

    public function setLabel(RoomCategoryEnum $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getPricePerNight(): ?string
    {
        return $this->pricePerNight;
    }

    public function setPricePerNight(string $pricePerNight): static
    {
        $this->pricePerNight = $pricePerNight;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection<int, RoomCategoryPreference>
     */
    public function getRoomCategoryPreferences(): Collection
    {
        return $this->roomCategoryPreferences;
    }

    public function addRoomCategoryPreference(RoomCategoryPreference $roomCategoryPreference): static
    {
        if (!$this->roomCategoryPreferences->contains($roomCategoryPreference)) {
            $this->roomCategoryPreferences->add($roomCategoryPreference);
            $roomCategoryPreference->addRoomCategory($this);
        }

        return $this;
    }

    public function removeRoomCategoryPreference(RoomCategoryPreference $roomCategoryPreference): static
    {
        if ($this->roomCategoryPreferences->removeElement($roomCategoryPreference)) {
            $roomCategoryPreference->removeRoomCategory($this);
        }

        return $this;
    }
}