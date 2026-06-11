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

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->statut = 'en_attente';
    }

    public function getId(): ?int { return $this->id; }
    public function getTotal(): ?string { return $this->total; }
    public function setTotal(string $total): static { $this->total = $total; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
}
