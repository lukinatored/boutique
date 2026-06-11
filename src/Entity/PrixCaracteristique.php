<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prix_caracteristique')]
class PrixCaracteristique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $supplement = null;

    #[ORM\ManyToOne(targetEntity: ValeurCaracteristique::class, inversedBy: 'prixCaracteristiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ValeurCaracteristique $valeurCaracteristique = null;

    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    public function getId(): ?int { return $this->id; }
    public function getSupplement(): ?string { return $this->supplement; }
    public function setSupplement(?string $supplement): static { $this->supplement = $supplement; return $this; }
    public function getValeurCaracteristique(): ?ValeurCaracteristique { return $this->valeurCaracteristique; }
    public function setValeurCaracteristique(?ValeurCaracteristique $valeurCaracteristique): static { $this->valeurCaracteristique = $valeurCaracteristique; return $this; }
    public function getProduit(): ?Produits { return $this->produit; }
    public function setProduit(?Produits $produit): static { $this->produit = $produit; return $this; }
}
