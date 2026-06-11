<?php
namespace App\Entity;

use App\Repository\PackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackRepository::class)]
#[ORM\Table(name: 'pack')]
class Pack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prixPromo = null;

    #[ORM\ManyToMany(targetEntity: Produits::class)]
    #[ORM\JoinTable(name: 'pack_produits')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): static { $this->prix = $prix; return $this; }
    public function getPrixPromo(): ?string { return $this->prixPromo; }
    public function setPrixPromo(?string $prixPromo): static { $this->prixPromo = $prixPromo; return $this; }
    public function getProduits(): Collection { return $this->produits; }
    public function addProduit(Produits $produit): static { if (!$this->produits->contains($produit)) { $this->produits->add($produit); } return $this; }
    public function removeProduit(Produits $produit): static { $this->produits->removeElement($produit); return $this; }
}
