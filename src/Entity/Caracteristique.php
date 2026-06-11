<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'caracteristique')]
class Caracteristique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'caracteristique', targetEntity: ValeurCaracteristique::class)]
    private Collection $valeurs;

    public function __construct()
    {
        $this->valeurs = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getValeurs(): Collection { return $this->valeurs; }
    public function addValeur(ValeurCaracteristique $valeur): static { if (!$this->valeurs->contains($valeur)) { $this->valeurs->add($valeur); $valeur->setCaracteristique($this); } return $this; }
    public function removeValeur(ValeurCaracteristique $valeur): static { if ($this->valeurs->removeElement($valeur) && $valeur->getCaracteristique() === $this) { $valeur->setCaracteristique(null); } return $this; }
}
