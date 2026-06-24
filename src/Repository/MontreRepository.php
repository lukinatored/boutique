<?php
<<<<<<< HEAD
=======

>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
namespace App\Repository;

use App\Entity\Montre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

<<<<<<< HEAD
=======
/**
 * @extends ServiceEntityRepository<Montre>
 */
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
class MontreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Montre::class);
    }
<<<<<<< HEAD
=======

//    /**
//     * @return Montre[] Returns an array of Montre objects
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

//    public function findOneBySomeField($value): ?Montre
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
