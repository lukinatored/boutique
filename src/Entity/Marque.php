<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'marque')]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $paysOrigine = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeCreation = null;

    #[ORM\OneToMany(mappedBy: 'marque', targetEntity: Montre::class)]
    private Collection $montres;

    public function __construct()
    {
        $this->montres = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getLogo(): ?string { return $this->logo; }
    public function setLogo(?string $logo): static { $this->logo = $logo; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getPaysOrigine(): ?string { return $this->paysOrigine; }
    public function setPaysOrigine(?string $paysOrigine): static { $this->paysOrigine = $paysOrigine; return $this; }
    public function getAnneeCreation(): ?int { return $this->anneeCreation; }
    public function setAnneeCreation(?int $anneeCreation): static { $this->anneeCreation = $anneeCreation; return $this; }
    public function getMontres(): Collection { return $this->montres; }
    public function addMontre(Montre $montre): static { if (!$this->montres->contains($montre)) { $this->montres->add($montre); $montre->setMarque($this); } return $this; }
    public function removeMontre(Montre $montre): static { if ($this->montres->removeElement($montre) && $montre->getMarque() === $this) { $montre->setMarque(null); } return $this; }
}
