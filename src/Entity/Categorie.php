<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categorie')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Montre::class)]
    private Collection $montres;

    public function __construct()
    {
        $this->montres = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getMontres(): Collection { return $this->montres; }
    public function addMontre(Montre $montre): static { if (!$this->montres->contains($montre)) { $this->montres->add($montre); $montre->setCategorie($this); } return $this; }
    public function removeMontre(Montre $montre): static { if ($this->montres->removeElement($montre) && $montre->getCategorie() === $this) { $montre->setCategorie(null); } return $this; }
}
