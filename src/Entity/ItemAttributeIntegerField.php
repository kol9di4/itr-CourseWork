<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ItemAttributeIntegerFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemAttributeIntegerFieldRepository::class)]
class ItemAttributeIntegerField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'itemAttributeIntegerFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    #[ORM\ManyToOne(inversedBy: 'itemAttributeIntegerFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomItemAttribute $customItemAttribute = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getCustomItemAttribute(): ?CustomItemAttribute
    {
        return $this->customItemAttribute;
    }

    public function setCustomItemAttribute(?CustomItemAttribute $customItemAttribute): static
    {
        $this->customItemAttribute = $customItemAttribute;

        return $this;
    }
}
