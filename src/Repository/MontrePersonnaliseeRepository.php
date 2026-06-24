<?php
namespace App\Repository;

use App\Entity\MontrePersonnalisee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MontrePersonnaliseeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MontrePersonnalisee::class);
    }

    public function findPubliees(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.estPubliee = true')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCreateur(int $createurId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.createur = :createurId')
            ->setParameter('createurId', $createurId)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPopular(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.estPubliee = true')
            ->orderBy('m.nbVues', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }
}
