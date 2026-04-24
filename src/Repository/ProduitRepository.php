<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // Exemples de méthodes personnalisées

    /**
     * Retourne les produits dont le stock est supérieur à zéro
     * 
     * @return Produit[]
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stock > 0')
            ->orderBy('p.Nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les produits les plus chers
     *
     * @param int $limit
     * @return Produit[]
     */
    public function findMostExpensive(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.prix', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
