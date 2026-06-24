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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descriptionDetaillee = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $etiquettes = [];

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $prixPromo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $galerie = [];

    // Caractéristiques de la montre
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mouvement = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $boitier = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $bracelet = null;

    #[ORM\Column(nullable: true)]
    private ?int $eauResistance = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $diametre = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $epaisseur = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Avis::class)]
    private Collection $avisList;

    public function __construct()
    {
        $this->etiquettes = [];
        $this->galerie = [];
        $this->avisList = new ArrayCollection();
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
    public function getDescriptionDetaillee(): ?string { return $this->descriptionDetaillee; }
    public function setDescriptionDetaillee(?string $descriptionDetaillee): static { $this->descriptionDetaillee = $descriptionDetaillee; return $this; }
    public function getEtiquettes(): ?array { return $this->etiquettes; }
    public function setEtiquettes(?array $etiquettes): static { $this->etiquettes = $etiquettes; return $this; }
    public function getPrixPromo(): ?string { return $this->prixPromo; }
    public function setPrixPromo(?string $prixPromo): static { $this->prixPromo = $prixPromo; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): static { $this->image = $image; return $this; }
    public function getGalerie(): ?array { return $this->galerie; }
    public function setGalerie(?array $galerie): static { $this->galerie = $galerie; return $this; }
    
    // Caractéristiques
    public function getMouvement(): ?string { return $this->mouvement; }
    public function setMouvement(?string $mouvement): static { $this->mouvement = $mouvement; return $this; }
    public function getBoitier(): ?string { return $this->boitier; }
    public function setBoitier(?string $boitier): static { $this->boitier = $boitier; return $this; }
    public function getBracelet(): ?string { return $this->bracelet; }
    public function setBracelet(?string $bracelet): static { $this->bracelet = $bracelet; return $this; }
    public function getEauResistance(): ?int { return $this->eauResistance; }
    public function setEauResistance(?int $eauResistance): static { $this->eauResistance = $eauResistance; return $this; }
    public function getDiametre(): ?string { return $this->diametre; }
    public function setDiametre(?string $diametre): static { $this->diametre = $diametre; return $this; }
    public function getEpaisseur(): ?string { return $this->epaisseur; }
    public function setEpaisseur(?string $epaisseur): static { $this->epaisseur = $epaisseur; return $this; }
    
    // Avis
    public function getAvisList(): Collection { return $this->avisList; }
    public function addAvis(Avis $avis): static { if (!$this->avisList->contains($avis)) { $this->avisList->add($avis); $avis->setProduit($this); } return $this; }
    public function removeAvis(Avis $avis): static { if ($this->avisList->removeElement($avis) && $avis->getProduit() === $this) { $avis->setProduit(null); } return $this; }
    public function getMoyenneNotes(): float
    {
        if ($this->avisList->count() === 0) return 4.5;
        $total = 0;
        foreach ($this->avisList as $avis) { $total += $avis->getNote(); }
        return round($total / $this->avisList->count(), 1);
    }
    public function getNbAvis(): int { return $this->avisList->count(); }
}
