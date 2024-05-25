<?php

namespace App\Entity;

use App\Enum\CustomAttributeEnum as CustomAttributeTypeEnum;
use App\Repository\CustomItemAttributeRepository;
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
