<?php
namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function findLastFactureNumber(): ?string
    {
        $result = $this->createQueryBuilder('f')
            ->select('f.numero')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        
        return $result ? $result['numero'] : null;
    }

    public function findByClient(int $clientId): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCommande(int $commandeId): ?Facture
    {
        return $this->createQueryBuilder('f')
            ->where('f.commande = :commandeId')
            ->setParameter('commandeId', $commandeId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
