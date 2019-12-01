<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonnageRepository")
 */
class Personnage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"connect_joueur","salle"})
     */
    private $guid;

    /**
     * @ORM\Column(type="integer")
     */
    private $degats;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Groups({"joueur"})
     */
    private $vie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"joueur"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"connect_joueur","joueur"})
     */
    private $totalVie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"joueur"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salle", inversedBy="personnages")
     * @Serializer\Groups({"connect_joueur"})
     */
    private $salle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuid(): ?int
    {
        return $this->guid;
    }

    public function setGuid(int $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getDegats(): ?int
    {
        return $this->degats;
    }

    public function setDegats(int $degats): self
    {
        $this->degats = $degats;

        return $this;
    }

    public function getVie(): ?int
    {
        return $this->vie;
    }

    public function setVie(?int $vie): self
    {
        $this->vie = $vie;

        return $this;
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

    public function getTotalVie(): ?int
    {
        return $this->totalVie;
    }

    public function setTotalVie(int $totalVie): self
    {
        $this->totalVie = $totalVie;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

}
