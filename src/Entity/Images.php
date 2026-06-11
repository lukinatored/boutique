<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'images')]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity: Produits::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getProduit(): ?Produits { return $this->produit; }
    public function setProduit(?Produits $produit): static { $this->produit = $produit; return $this; }
}
