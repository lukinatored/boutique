<?php
namespace App\Entity;

use App\Repository\AcheterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcheterRepository::class)]
#[ORM\Table(name: 'acheter')]
class Acheter
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Commande::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column(type: 'smallint')]
    private ?int $quantite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $prix = null;

    public function getProduit(): ?Produits { return $this->produit; }
    public function setProduit(?Produits $produit): static { $this->produit = $produit; return $this; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }
    public function getQuantite(): ?int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }
    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): static { $this->prix = $prix; return $this; }
}
