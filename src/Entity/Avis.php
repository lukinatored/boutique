<?php
namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\Table(name: 'avis')]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Produits::class, inversedBy: 'avisList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produits $produit = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: 'smallint')]
    private ?int $note = null;

    #[ORM\Column(type: 'text')]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getProduit(): ?Produits { return $this->produit; }
    public function setProduit(?Produits $produit): static { $this->produit = $produit; return $this; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }
    public function getNote(): ?int { return $this->note; }
    public function setNote(int $note): static { $this->note = $note; return $this; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(string $commentaire): static { $this->commentaire = $commentaire; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
}
