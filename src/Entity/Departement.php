<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'departement')]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'departements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    #[ORM\OneToMany(mappedBy: 'departement', targetEntity: Ville::class)]
    private Collection $villes;

    public function __construct()
    {
        $this->villes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }
    public function getRegion(): ?Region { return $this->region; }
    public function setRegion(?Region $region): static { $this->region = $region; return $this; }
    public function getVilles(): Collection { return $this->villes; }
}
