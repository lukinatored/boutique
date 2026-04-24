<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'Images')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_images', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nomimage = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'IDProduits', referencedColumnName: 'IDProduits', nullable: false, onDelete: 'CASCADE')]
    private ?Produit $produit = null;

    public function getId(): ?int { return $this->id; }
    public function getNomimage(): ?string { return $this->nomimage; }
    public function setNomimage(string $nomimage): self { $this->nomimage = $nomimage; return $this; }
    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): self { $this->produit = $produit; return $this; }
}
