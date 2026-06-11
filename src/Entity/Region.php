<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'region')]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Departement::class)]
    private Collection $departements;

    public function __construct()
    {
        $this->departements = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDepartements(): Collection { return $this->departements; }
    public function addDepartement(Departement $departement): static { if (!$this->departements->contains($departement)) { $this->departements->add($departement); $departement->setRegion($this); } return $this; }
    public function removeDepartement(Departement $departement): static { if ($this->departements->removeElement($departement) && $departement->getRegion() === $this) { $departement->setRegion(null); } return $this; }
}
