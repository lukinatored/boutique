<?php
namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ORM\Table(name: 'facture')]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $numero = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $tva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalTTC = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'en_attente';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->tva = '20.00';
        $this->statut = 'en_attente';
    }

    public function getId(): ?int { return $this->id; }
    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }
    public function getTotal(): ?string { return $this->total; }
    public function setTotal(string $total): static { $this->total = $total; return $this; }
    public function getTva(): ?string { return $this->tva; }
    public function setTva(string $tva): static { $this->tva = $tva; return $this; }
    public function getTotalTTC(): ?string { return $this->totalTTC; }
    public function setTotalTTC(string $totalTTC): static { $this->totalTTC = $totalTTC; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
}
