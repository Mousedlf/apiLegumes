<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Vegetable>
     */
    #[ORM\ManyToMany(targetEntity: Vegetable::class, mappedBy: 'season')]
    private Collection $vegetables;

    public function __construct()
    {
        $this->vegetables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Vegetable>
     */
    public function getVegetables(): Collection
    {
        return $this->vegetables;
    }

    public function addVegetable(Vegetable $vegetable): static
    {
        if (!$this->vegetables->contains($vegetable)) {
            $this->vegetables->add($vegetable);
            $vegetable->addSeason($this);
        }

        return $this;
    }

    public function removeVegetable(Vegetable $vegetable): static
    {
        if ($this->vegetables->removeElement($vegetable)) {
            $vegetable->removeSeason($this);
        }

        return $this;
    }
}
