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
        return $this->findBy(['produit' => $produitId], ['createdAt' => 'DESC']);
    }

    public function getAverageNote(int $produitId): float
    {
        $result = $this->createQueryBuilder('a')
            ->select('AVG(a.note)')
            ->where('a.produit = :produitId')
            ->setParameter('produitId', $produitId)
            ->getQuery()
            ->getSingleScalarResult();
        return round($result ?? 0, 1);
    }
}
