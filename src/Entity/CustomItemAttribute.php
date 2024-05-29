<?php

namespace App\Entity;

use App\Enum\CustomAttributeEnum as CustomAttributeTypeEnum;
use App\Repository\CustomItemAttributeRepository;
use App\Validator\CollectionCustomAttribute;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CustomItemAttributeRepository::class)]
class CustomItemAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:3,max: 255)]
    private ?string $name = null;

//    #[ORM\Column(length: 255)]
//    private ?string $type = null;
    #[ORM\Column(length: 10, enumType: CustomAttributeTypeEnum::class)]
    private ?CustomAttributeTypeEnum $type = null;

    #[ORM\ManyToOne(inversedBy: 'customItemAttributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemCollection $itemCollection = null;

    #[ORM\OneToMany(targetEntity: ItemAttributeBooleanField::class, mappedBy: 'customItemAttribute', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeBooleanFields;

    #[ORM\OneToMany(targetEntity: ItemAttributeDateField::class, mappedBy: 'customItemAttribute', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeDateFields;

    #[ORM\OneToMany(targetEntity: ItemAttributeIntegerField::class, mappedBy: 'customItemAttribute', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeIntegerFields;

    #[ORM\OneToMany(targetEntity: ItemAttributeStringField::class, mappedBy: 'customItemAttribute', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeStringFields;

    #[ORM\OneToMany(targetEntity: ItemAttributeTextField::class, mappedBy: 'customItemAttribute', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeTextFields;

    public function __construct()
    {
        $this->type = CustomAttributeTypeEnum::Integer;
//...
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

    public function getType(): ?CustomAttributeTypeEnum
    {
        return $this->type;
    }

    public function setType(CustomAttributeTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getItemCollection(): ?ItemCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(?ItemCollection $itemCollection): static
    {
        $this->itemCollection = $itemCollection;

        return $this;
    }
}
