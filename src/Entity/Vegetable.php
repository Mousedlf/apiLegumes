<?php

namespace App\Entity;

use App\Repository\VegetableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VegetableRepository::class)]
class Vegetable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["color:read", "season:read", "vegetables:read", "vegetable:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["color:read", "season:read", "vegetables:read", "vegetable:read"])]
    private ?string $name = null;

    /**
     * @var Collection<int, Color>
     */
    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'vegetables')]
    #[Groups(["vegetable:read"])]
    private Collection $color;

    /**
     * @var Collection<int, Season>
     */
    #[ORM\ManyToMany(targetEntity: Season::class, inversedBy: 'vegetables')]
    #[Groups(["vegetable:read"])]
    private Collection $season;

    #[ORM\ManyToOne(inversedBy: 'createdVegetables')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["vegetable:read"])]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->color = new ArrayCollection();
        $this->season = new ArrayCollection();
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

    /**
     * @return Collection<int, Color>
     */
    public function getColor(): Collection
    {
        return $this->color;
    }

    public function addColor(Color $color): static
    {
        if (!$this->color->contains($color)) {
            $this->color->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        $this->color->removeElement($color);

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeason(): Collection
    {
        return $this->season;
    }

    public function addSeason(Season $season): static
    {
        if (!$this->season->contains($season)) {
            $this->season->add($season);
        }

        return $this;
    }

    public function removeSeason(Season $season): static
    {
        $this->season->removeElement($season);

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
