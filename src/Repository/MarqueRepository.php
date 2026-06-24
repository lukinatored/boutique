<?php
<<<<<<< HEAD
=======

>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
namespace App\Repository;

use App\Entity\Marque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

<<<<<<< HEAD
=======
/**
 * @extends ServiceEntityRepository<Marque>
 */
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
class MarqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marque::class);
    }
<<<<<<< HEAD
=======

    //    /**
    //     * @return Marque[] Returns an array of Marque objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Marque
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
}
