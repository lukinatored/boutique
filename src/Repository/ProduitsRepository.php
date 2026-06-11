<?php
namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    public function findLatest(int $limit = 8): array
    {
        return $this->findBy([], ['id' => 'DESC'], $limit);
    }

    public function searchByName(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }
}
