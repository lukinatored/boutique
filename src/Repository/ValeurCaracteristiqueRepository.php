<?php
namespace App\Repository;

use App\Entity\ValeurCaracteristique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ValeurCaracteristiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValeurCaracteristique::class);
    }
}
