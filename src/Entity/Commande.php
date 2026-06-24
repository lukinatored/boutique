<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'commande')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateExpedition = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateLivraison = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $numeroSuivi = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaireLivraison = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $historique = [];

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $vendeurContacte = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateContact = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Acheter::class)]
    private Collection $details;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Facture::class)]
    private Collection $factures;

    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->statut = 'en_attente';
        $this->historique = [];
        $this->vendeurContacte = false;
    }

    public function getId(): ?int { return $this->id; }
    public function getTotal(): ?string { return $this->total; }
    public function setTotal(string $total): static { $this->total = $total; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function getDateExpedition(): ?\DateTimeInterface { return $this->dateExpedition; }
    public function setDateExpedition(?\DateTimeInterface $dateExpedition): static { $this->dateExpedition = $dateExpedition; return $this; }
    public function getDateLivraison(): ?\DateTimeInterface { return $this->dateLivraison; }
    public function setDateLivraison(?\DateTimeInterface $dateLivraison): static { $this->dateLivraison = $dateLivraison; return $this; }
    public function getNumeroSuivi(): ?string { return $this->numeroSuivi; }
    public function setNumeroSuivi(?string $numeroSuivi): static { $this->numeroSuivi = $numeroSuivi; return $this; }
    public function getCommentaireLivraison(): ?string { return $this->commentaireLivraison; }
    public function setCommentaireLivraison(?string $commentaireLivraison): static { $this->commentaireLivraison = $commentaireLivraison; return $this; }
    public function getHistorique(): ?array { return $this->historique; }
    public function setHistorique(?array $historique): static { $this->historique = $historique; return $this; }
    public function addHistorique(string $etape, string $message): static {
        $historique = $this->historique ?? [];
        $historique[] = [
            'etape' => $etape,
            'message' => $message,
            'date' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        $this->historique = $historique;
        return $this;
    }
    public function isVendeurContacte(): ?bool { return $this->vendeurContacte; }
    public function setVendeurContacte(bool $vendeurContacte): static { $this->vendeurContacte = $vendeurContacte; return $this; }
    public function getDateContact(): ?\DateTimeInterface { return $this->dateContact; }
    public function setDateContact(?\DateTimeInterface $dateContact): static { $this->dateContact = $dateContact; return $this; }
    public function getDetails(): Collection { return $this->details; }
    public function getFactures(): Collection { return $this->factures; }
}
