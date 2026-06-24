<?php
namespace App\Entity;

use App\Repository\AlerteStockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlerteStockRepository::class)]
#[ORM\Table(name: 'alerte_stock')]
class AlerteStock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    #[ORM\Column]
    private ?bool $active = true;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->active = true;
    }

    public function getId(): ?int { return $this->id; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }
    public function getProduit(): ?Produits { return $this->produit; }
    public function setProduit(?Produits $produit): static { $this->produit = $produit; return $this; }
    public function isActive(): ?bool { return $this->active; }
    public function setActive(bool $active): static { $this->active = $active; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
}
