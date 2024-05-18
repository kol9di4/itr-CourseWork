<?php

namespace App\Entity;

use App\Repository\ItemCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemCollectionRepository::class)]
class ItemCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $image = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'itemCollection')]
    private Collection $items;

    #[ORM\ManyToOne(inversedBy: 'itemCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, CustomItemAttribute>
     */
    #[ORM\OneToMany(targetEntity: CustomItemAttribute::class, mappedBy: 'itemCollection', orphanRemoval: true)]
    private Collection $customItemAttributes;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->customItemAttributes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getname(): ?string
    {
        return $this->name;
    }

    public function setname(string $name): static
    {
        $this->name = $name;

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

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): static
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setItemCollection($this);
        }

        return $this;
    }
    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItemCollection() === $this) {
                $item->setItemCollection(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, CustomItemAttribute>
     */
    public function getCustomItemAttributes(): Collection
    {
        return $this->customItemAttributes;
    }

    public function addCustomItemAttribute(CustomItemAttribute $customItemAttribute): static
    {
        if (!$this->customItemAttributes->contains($customItemAttribute)) {
            $this->customItemAttributes->add($customItemAttribute);
            $customItemAttribute->setItemCollection($this);
        }

        return $this;
    }

    public function removeCustomItemAttribute(CustomItemAttribute $customItemAttribute): static
    {
        if ($this->customItemAttributes->removeElement($customItemAttribute)) {
            // set the owning side to null (unless already changed)
            if ($customItemAttribute->getItemCollection() === $this) {
                $customItemAttribute->setItemCollection(null);
            }
        }

        return $this;
    }
}
