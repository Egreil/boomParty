<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampusRepository::class)]
class Campus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\OneToMany(targetEntity: Participant::class, mappedBy: 'campus')]
    private Collection $idCampus;

    public function __construct()
    {
        $this->idCampus = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getIdCampus(): Collection
    {
        return $this->idCampus;
    }

    public function addIdCampus(Participant $idCampus): static
    {
        if (!$this->idCampus->contains($idCampus)) {
            $this->idCampus->add($idCampus);
            $idCampus->setCampus($this);
        }

        return $this;
    }

    public function removeIdCampus(Participant $idCampus): static
    {
        if ($this->idCampus->removeElement($idCampus)) {
            // set the owning side to null (unless already changed)
            if ($idCampus->getCampus() === $this) {
                $idCampus->setCampus(null);
            }
        }

        return $this;
    }


}
