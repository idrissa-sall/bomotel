<?php

namespace App\Entity;

use App\Repository\RoomCategoryPreferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomCategoryPreferenceRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PREF', fields: ['label'])]
#[UniqueEntity(
    fields: ['label'],
    message: 'Cette préférence existe déjà.'
)]
class RoomCategoryPreference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le libellé ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $label = null;

    /**
     * @var Collection<int, RoomCategory>
     */
    #[ORM\ManyToMany(targetEntity: RoomCategory::class, inversedBy: 'roomCategoryPreferences')]
    private Collection $roomCategory;

    public function __construct()
    {
        $this->roomCategory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, RoomCategory>
     */
    public function getRoomCategory(): Collection
    {
        return $this->roomCategory;
    }

    public function addRoomCategory(RoomCategory $roomCategory): static
    {
        if (!$this->roomCategory->contains($roomCategory)) {
            $this->roomCategory->add($roomCategory);
        }

        return $this;
    }

    public function removeRoomCategory(RoomCategory $roomCategory): static
    {
        $this->roomCategory->removeElement($roomCategory);

        return $this;
    }
}