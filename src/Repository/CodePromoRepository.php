<?php
namespace App\Repository;

use App\Entity\CodePromo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CodePromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodePromo::class);
    }

    public function findValidCode(string $code): ?CodePromo
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('c')
            ->where('c.code = :code')
            ->andWhere('c.actif = true')
            ->andWhere('c.dateExpiration > :now')
            ->andWhere('c.nbUtilisations < c.nbUtilisationsMax')
            ->setParameter('code', $code)
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
