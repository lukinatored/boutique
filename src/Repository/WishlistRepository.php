<?php
namespace App\Repository;

use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlist::class);
    }

    public function findWishlistByUser(int $userId): array
    {
        return $this->createQueryBuilder('w')
            ->leftJoin('w.produit', 'p')
            ->addSelect('p')
            ->where('w.client = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('w.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function isInWishlist(int $userId, int $produitId): bool
    {
        $result = $this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->where('w.client = :userId')
            ->andWhere('w.produit = :produitId')
            ->setParameter('userId', $userId)
            ->setParameter('produitId', $produitId)
            ->getQuery()
            ->getSingleScalarResult();
        
        return $result > 0;
    }
}
