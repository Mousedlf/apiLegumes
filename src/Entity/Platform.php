<?php

namespace App\Entity;

use App\Repository\PlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
class Platform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500)]
    private ?string $adminKey = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'fromPlatform')]
    private Collection $linkedClients;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();

        $faker = \Faker\Factory::create();
        $randomString = substr(str_shuffle(str_repeat($faker->lexify('??????') . $faker->numerify('###'), 10)), 0, 50);
        $this->adminKey = $randomString;
        $this->linkedClients = new ArrayCollection();
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

    public function getAdminKey(): ?string
    {
        return $this->adminKey;
    }

    public function setAdminKey(string $adminKey): static
    {
        $this->adminKey = $adminKey;

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

    /**
     * @return Collection<int, Client>
     */
    public function getLinkedClients(): Collection
    {
        return $this->linkedClients;
    }

    public function addLinkedClient(Client $linkedClient): static
    {
        if (!$this->linkedClients->contains($linkedClient)) {
            $this->linkedClients->add($linkedClient);
            $linkedClient->setFromPlatform($this);
        }

        return $this;
    }

    public function removeLinkedClient(Client $linkedClient): static
    {
        if ($this->linkedClients->removeElement($linkedClient)) {
            // set the owning side to null (unless already changed)
            if ($linkedClient->getFromPlatform() === $this) {
                $linkedClient->setFromPlatform(null);
            }
        }

        return $this;
    }
}
