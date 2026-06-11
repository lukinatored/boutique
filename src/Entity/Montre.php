<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'montre')]
class Montre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Marque::class, inversedBy: 'montres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Marque $marque = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'montres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    public function getId(): ?int { return $this->id; }
    public function getMarque(): ?Marque { return $this->marque; }
    public function setMarque(?Marque $marque): static { $this->marque = $marque; return $this; }
    public function getCategorie(): ?Categorie { return $this->categorie; }
    public function setCategorie(?Categorie $categorie): static { $this->categorie = $categorie; return $this; }
    public function getProduit(): ?Produits { return $this->produit; }
    public function setProduit(?Produits $produit): static { $this->produit = $produit; return $this; }
}
