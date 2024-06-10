<?php

namespace App\Entity;

use App\Repository\IssueRepository;
use Doctrine\ORM\Mapping as ORM;
use JiraCloud\Issue\IssueService;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
class Issue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $idJira = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdJira(): ?string
    {
        return $this->idJira;
    }

    public function setIdJira(string $idJira): static
    {
        $this->idJira = $idJira;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function getStatus(): string{
        $issueService = new IssueService();
        $fields = [
            'status'
        ];
        $issue = $issueService->get($this->idJira);

        return $issue->fields->status->name;
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
