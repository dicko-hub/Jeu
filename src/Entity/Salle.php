<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SalleRepository")
 */
class Salle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"connect_joueur","salle"})
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     * @Serializer\Groups({"connect_joueur","salle"})
     */
    private $passages = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personnage", mappedBy="salle")
     * @Serializer\Groups({"connect_joueur","salle"})
     */
    private $personnages;

    public function __construct()
    {
        $this->personnages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPassages(): ?array
    {
        return $this->passages;
    }

    public function setPassages(array $passages): self
    {
        $this->passages = $passages;

        return $this;
    }

    /**
     * @return Collection|Personnage[]
     */
    public function getPersonnages(): Collection
    {
        return $this->personnages;
    }

    public function addPersonnage(Personnage $personnage): self
    {
        if (!$this->personnages->contains($personnage)) {
            $this->personnages[] = $personnage;
            $personnage->setSalle($this);
        }

        return $this;
    }

    public function removePersonnage(Personnage $personnage): self
    {
        if ($this->personnages->contains($personnage)) {
            $this->personnages->removeElement($personnage);
            // set the owning side to null (unless already changed)
            if ($personnage->getSalle() === $this) {
                $personnage->setSalle(null);
            }
        }

        return $this;
    }
}
