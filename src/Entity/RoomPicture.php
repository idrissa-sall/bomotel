<?php

namespace App\Entity;

use App\Repository\RoomPictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomPictureRepository::class)]
class RoomPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La description alternative est obligatoire.', normalizer: 'trim')]
    #[Assert\Length(max: 50, maxMessage: 'La description alternative ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $alt = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L’ordre d’affichage est obligatoire.")]
    #[Assert\PositiveOrZero(message: "L’ordre d’affichage doit être positif ou nul.")]
    private ?int $position = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'roomPictures')]
    #[Assert\NotNull(message: 'Veuillez sélectionner une catégorie de chambre.')]
    private ?RoomCategory $roomCategory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

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

    public function getRoomCategory(): ?RoomCategory
    {
        return $this->roomCategory;
    }

    public function setRoomCategory(?RoomCategory $roomCategory): static
    {
        $this->roomCategory = $roomCategory;

        return $this;
    }
}
