<?php
namespace App\Entity;

use App\Repository\CodePromoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodePromoRepository::class)]
#[ORM\Table(name: 'code_promo')]
class CodePromo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $reduction = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null; // 'pourcentage' ou 'fixe'

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montantMin = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $nbUtilisations = 0;

    #[ORM\Column(type: 'integer')]
    private ?int $nbUtilisationsMax = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private ?bool $actif = true;

    public function getId(): ?int { return $this->id; }
    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }
    public function getReduction(): ?string { return $this->reduction; }
    public function setReduction(string $reduction): static { $this->reduction = $reduction; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }
    public function getDateExpiration(): ?\DateTimeInterface { return $this->dateExpiration; }
    public function setDateExpiration(\DateTimeInterface $dateExpiration): static { $this->dateExpiration = $dateExpiration; return $this; }
    public function getMontantMin(): ?string { return $this->montantMin; }
    public function setMontantMin(?string $montantMin): static { $this->montantMin = $montantMin; return $this; }
    public function getNbUtilisations(): ?int { return $this->nbUtilisations; }
    public function setNbUtilisations(int $nbUtilisations): static { $this->nbUtilisations = $nbUtilisations; return $this; }
    public function getNbUtilisationsMax(): ?int { return $this->nbUtilisationsMax; }
    public function setNbUtilisationsMax(int $nbUtilisationsMax): static { $this->nbUtilisationsMax = $nbUtilisationsMax; return $this; }
    public function isActif(): ?bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }
}
