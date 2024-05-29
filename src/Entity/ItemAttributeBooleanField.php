<?php

namespace App\Entity;

use App\Repository\ItemAttributeBooleanFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemAttributeBooleanFieldRepository::class)]
class ItemAttributeBooleanField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $value = null;

    #[ORM\ManyToOne(inversedBy: 'itemAttributeBooleanFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    #[ORM\ManyToOne(inversedBy: 'itemAttributeBooleanFields', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomItemAttribute $customItemAttribute = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValue(): ?bool
    {
        return $this->value;
    }

    public function setValue(bool $value): static
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
