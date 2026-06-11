<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'valeur_caracteristique')]
class ValeurCaracteristique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $valeur = null;

    #[ORM\ManyToOne(targetEntity: Caracteristique::class, inversedBy: 'valeurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Caracteristique $caracteristique = null;

    #[ORM\OneToMany(mappedBy: 'valeurCaracteristique', targetEntity: PrixCaracteristique::class)]
    private Collection $prixCaracteristiques;

    public function __construct()
    {
        $this->prixCaracteristiques = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getValeur(): ?string { return $this->valeur; }
    public function setValeur(string $valeur): static { $this->valeur = $valeur; return $this; }
    public function getCaracteristique(): ?Caracteristique { return $this->caracteristique; }
    public function setCaracteristique(?Caracteristique $caracteristique): static { $this->caracteristique = $caracteristique; return $this; }
    public function getPrixCaracteristiques(): Collection { return $this->prixCaracteristiques; }
}
