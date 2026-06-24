<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'client')]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $pointsFidelite = 0;

    #[ORM\Column(length: 20, options: ['default' => 'bronze'])]
    private ?string $niveau = 'bronze';

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $totalAchats = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $nbCommandes = 0;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->pointsFidelite = 0;
        $this->niveau = 'bronze';
        $this->totalAchats = 0;
        $this->nbCommandes = 0;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $adresse): static { $this->adresse = $adresse; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }
    public function getRole(): ?Role { return $this->role; }
    public function setRole(?Role $role): static { $this->role = $role; return $this; }
    public function getPointsFidelite(): ?int { return $this->pointsFidelite; }
    public function setPointsFidelite(int $pointsFidelite): static { $this->pointsFidelite = $pointsFidelite; return $this; }
    public function getNiveau(): ?string { return $this->niveau; }
    public function setNiveau(string $niveau): static { $this->niveau = $niveau; return $this; }
    public function getTotalAchats(): ?int { return $this->totalAchats; }
    public function setTotalAchats(int $totalAchats): static { $this->totalAchats = $totalAchats; return $this; }
    public function getNbCommandes(): ?int { return $this->nbCommandes; }
    public function setNbCommandes(int $nbCommandes): static { $this->nbCommandes = $nbCommandes; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getCommandes(): Collection { return $this->commandes; }
    
    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return [$this->role?->getNom() ?? 'ROLE_USER']; }
    public function eraseCredentials(): void {}
}
