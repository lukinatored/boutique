<?php
namespace App\Entity;

use App\Repository\MontrePersonnaliseeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MontrePersonnaliseeRepository::class)]
#[ORM\Table(name: 'montre_personnalisee')]
class MontrePersonnalisee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $boitier = null;

    #[ORM\Column(length: 100)]
    private ?string $bracelet = null;

    #[ORM\Column(length: 100)]
    private ?string $mouvement = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private ?int $stock = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private ?int $vendus = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $createur = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    private ?bool $estPubliee = false;

    #[ORM\Column]
    private ?int $nbVues = 0;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->estPubliee = false;
        $this->nbVues = 0;
        $this->stock = 0;
        $this->vendus = 0;
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getBoitier(): ?string { return $this->boitier; }
    public function setBoitier(string $boitier): static { $this->boitier = $boitier; return $this; }
    public function getBracelet(): ?string { return $this->bracelet; }
    public function setBracelet(string $bracelet): static { $this->bracelet = $bracelet; return $this; }
    public function getMouvement(): ?string { return $this->mouvement; }
    public function setMouvement(string $mouvement): static { $this->mouvement = $mouvement; return $this; }
    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): static { $this->prix = $prix; return $this; }
    public function getStock(): ?int { return $this->stock; }
    public function setStock(int $stock): static { $this->stock = $stock; return $this; }
    public function getVendus(): ?int { return $this->vendus; }
    public function setVendus(int $vendus): static { $this->vendus = $vendus; return $this; }
    public function incrementVendus(): static { $this->vendus++; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): static { $this->image = $image; return $this; }
    public function getSource(): ?string { return $this->source; }
    public function setSource(?string $source): static { $this->source = $source; return $this; }
    public function getCreateur(): ?Client { return $this->createur; }
    public function setCreateur(?Client $createur): static { $this->createur = $createur; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function isEstPubliee(): ?bool { return $this->estPubliee; }
    public function setEstPubliee(bool $estPubliee): static { $this->estPubliee = $estPubliee; return $this; }
    public function getNbVues(): ?int { return $this->nbVues; }
    public function setNbVues(int $nbVues): static { $this->nbVues = $nbVues; return $this; }
    public function incrementVues(): static { $this->nbVues++; return $this; }
}
