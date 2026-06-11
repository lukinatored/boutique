<?php
namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
#[ORM\Table(name: 'livraison')]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateExpedition = null;

    #[ORM\OneToOne(targetEntity: Commande::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->statut = 'preparation';
        $this->dateExpedition = new \DateTime('+2 days');
    }

    public function getId(): ?int { return $this->id; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $adresse): static { $this->adresse = $adresse; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getDateExpedition(): ?\DateTimeInterface { return $this->dateExpedition; }
    public function setDateExpedition(\DateTimeInterface $dateExpedition): static { $this->dateExpedition = $dateExpedition; return $this; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }
}
