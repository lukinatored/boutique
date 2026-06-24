<?php
<<<<<<< HEAD
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'marque')]
=======

namespace App\Entity;

use App\Repository\MarqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< HEAD
    #[ORM\Column(length: 100, unique: true)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'marque', targetEntity: Montre::class)]
    private Collection $montres;

    public function __construct()
    {
        $this->montres = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getMontres(): Collection { return $this->montres; }
=======
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
}
