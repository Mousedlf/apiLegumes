<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbOfAvailableRequests = null;

    #[ORM\Column]
    private ?int $nbUsedRequests = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'linkedClients')]
    private ?Platform $fromPlatform = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $apiKey = null;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();

    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNbOfAvailableRequests(): ?int
    {
        return $this->nbOfAvailableRequests;
    }

    public function setNbOfAvailableRequests(int $nbOfAvailableRequests): static
    {
        $this->nbOfAvailableRequests = $nbOfAvailableRequests;

        return $this;
    }

    public function getNbUsedRequests(): ?int
    {
        return $this->nbUsedRequests;
    }

    public function setNbUsedRequests(int $nbUsedRequests): static
    {
        $this->nbUsedRequests = $nbUsedRequests;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFromPlatform(): ?Platform
    {
        return $this->fromPlatform;
    }

    public function setFromPlatform(?Platform $fromPlatform): static
    {
        $this->fromPlatform = $fromPlatform;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
