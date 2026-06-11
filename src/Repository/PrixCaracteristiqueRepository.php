<?php
namespace App\Repository;

use App\Entity\PrixCaracteristique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PrixCaracteristiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrixCaracteristique::class);
    }
}
