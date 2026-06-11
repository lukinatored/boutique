<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'produits')]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $stock = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Images::class)]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getStock(): ?int { return $this->stock; }
    public function setStock(int $stock): static { $this->stock = $stock; return $this; }
    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): static { $this->prix = $prix; return $this; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getImages(): Collection { return $this->images; }
    public function addImage(Images $image): static { if (!$this->images->contains($image)) { $this->images->add($image); $image->setProduit($this); } return $this; }
    public function removeImage(Images $image): static { if ($this->images->removeElement($image) && $image->getProduit() === $this) { $image->setProduit(null); } return $this; }
}
