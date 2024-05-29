<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'items', orphanRemoval: true)]
    #[Assert\NotBlank]
    private Collection $tag;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemCollection $itemCollection = null;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'item', orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'item', orphanRemoval: true, fetch: 'EAGER')]
    private Collection $comments;

    /**
     * @var Collection<int, ItemAttributeStringField>
     */
    #[ORM\OneToMany(targetEntity: ItemAttributeStringField::class, mappedBy: 'item', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeStringFields;

    /**
     * @var Collection<int, ItemAttributeIntegerField>
     */
    #[ORM\OneToMany(targetEntity: ItemAttributeIntegerField::class, mappedBy: 'item', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeIntegerFields;

    /**
     * @var Collection<int, ItemAttributeTextField>
     */
    #[ORM\OneToMany(targetEntity: ItemAttributeTextField::class, mappedBy: 'item', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeTextFields;

    /**
     * @var Collection<int, ItemAttributeBooleanField>
     */
    #[ORM\OneToMany(targetEntity: ItemAttributeBooleanField::class, mappedBy: 'item', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeBooleanFields;

    /**
     * @var Collection<int, ItemAttributeDateField>
     */
    #[ORM\OneToMany(targetEntity: ItemAttributeDateField::class, mappedBy: 'item', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $itemAttributeDateFields;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $views = null;

    public function __construct()
    {
        $this->tag = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->itemAttributeStringFields = new ArrayCollection();
        $this->itemAttributeIntegerFields = new ArrayCollection();
        $this->itemAttributeTextFields = new ArrayCollection();
        $this->itemAttributeBooleanFields = new ArrayCollection();
        $this->itemAttributeDateFields = new ArrayCollection();
        $this->dateAdd = new \DateTime();
        $this->setViews(0);
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

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): static
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tag->contains($tag)) {
            $this->tag->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tag->removeElement($tag);

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

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setItem($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getItem() === $this) {
                $like->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setItem($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getItem() === $this) {
                $comment->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemAttributeStringField>
     */
    public function getItemAttributeStringFields(): Collection
    {
        return $this->itemAttributeStringFields;
    }

    public function addItemAttributeStringField(ItemAttributeStringField $itemAttributeStringField): static
    {
        if (!$this->itemAttributeStringFields->contains($itemAttributeStringField)) {
            $this->itemAttributeStringFields->add($itemAttributeStringField);
            $itemAttributeStringField->setItem($this);
        }

        return $this;
    }

    public function removeItemAttributeStringField(ItemAttributeStringField $itemAttributeStringField): static
    {
        if ($this->itemAttributeStringFields->removeElement($itemAttributeStringField)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeStringField->getItem() === $this) {
                $itemAttributeStringField->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemAttributeIntegerField>
     */
    public function getItemAttributeIntegerFields(): Collection
    {
        return $this->itemAttributeIntegerFields;
    }

    public function addItemAttributeIntegerField(ItemAttributeIntegerField $itemAttributeIntegerField): static
    {
        if (!$this->itemAttributeIntegerFields->contains($itemAttributeIntegerField)) {
            $this->itemAttributeIntegerFields->add($itemAttributeIntegerField);
            $itemAttributeIntegerField->setItem($this);
        }

        return $this;
    }

    public function removeItemAttributeIntegerField(ItemAttributeIntegerField $itemAttributeIntegerField): static
    {
        if ($this->itemAttributeIntegerFields->removeElement($itemAttributeIntegerField)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeIntegerField->getItem() === $this) {
                $itemAttributeIntegerField->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemAttributeTextField>
     */
    public function getItemAttributeTextFields(): Collection
    {
        return $this->itemAttributeTextFields;
    }

    public function addItemAttributeTextField(ItemAttributeTextField $itemAttributeTextField): static
    {
        if (!$this->itemAttributeTextFields->contains($itemAttributeTextField)) {
            $this->itemAttributeTextFields->add($itemAttributeTextField);
            $itemAttributeTextField->setItem($this);
        }

        return $this;
    }

    public function removeItemAttributeTextField(ItemAttributeTextField $itemAttributeTextField): static
    {
        if ($this->itemAttributeTextFields->removeElement($itemAttributeTextField)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeTextField->getItem() === $this) {
                $itemAttributeTextField->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemAttributeBooleanField>
     */
    public function getItemAttributeBooleanFields(): Collection
    {
        return $this->itemAttributeBooleanFields;
    }

    public function addItemAttributeBooleanField(ItemAttributeBooleanField $itemAttributeBooleanField): static
    {
        if (!$this->itemAttributeBooleanFields->contains($itemAttributeBooleanField)) {
            $this->itemAttributeBooleanFields->add($itemAttributeBooleanField);
            $itemAttributeBooleanField->setItem($this);
        }

        return $this;
    }

    public function removeItemAttributeBooleanField(ItemAttributeBooleanField $itemAttributeBooleanField): static
    {
        if ($this->itemAttributeBooleanFields->removeElement($itemAttributeBooleanField)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeBooleanField->getItem() === $this) {
                $itemAttributeBooleanField->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemAttributeDateField>
     */
    public function getItemAttributeDateFields(): Collection
    {
        return $this->itemAttributeDateFields;
    }

    public function addItemAttributeDateField(ItemAttributeDateField $itemAttributeDateField): static
    {
        if (!$this->itemAttributeDateFields->contains($itemAttributeDateField)) {
            $this->itemAttributeDateFields->add($itemAttributeDateField);
            $itemAttributeDateField->setItem($this);
        }

        return $this;
    }

    public function removeItemAttributeDateField(ItemAttributeDateField $itemAttributeDateField): static
    {
        if ($this->itemAttributeDateFields->removeElement($itemAttributeDateField)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeDateField->getItem() === $this) {
                $itemAttributeDateField->setItem(null);
            }
        }

        return $this;
    }

    public function getViews(): ?string
    {
        return $this->views;
    }

    public function setViews(string $views): static
    {
        $this->views = $views;

        return $this;
    }
}
