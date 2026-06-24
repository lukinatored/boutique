<?php
namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function findByProduit(int $produitId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.produit = :produitId')
            ->setParameter('produitId', $produitId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMoyenne(int $produitId): float
    {
        $result = $this->createQueryBuilder('a')
            ->select('AVG(a.note)')
            ->where('a.produit = :produitId')
            ->setParameter('produitId', $produitId)
            ->getQuery()
            ->getSingleScalarResult();
        return round($result ?? 0, 1);
    }

    public function countByProduit(int $produitId): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.produit = :produitId')
            ->setParameter('produitId', $produitId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
