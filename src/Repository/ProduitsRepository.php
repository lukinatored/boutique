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

    public function searchAutocomplete(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.nom, p.prix, p.stock')
            ->where('p.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('p.nom', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function searchWithFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($filters['search'])) {
            $qb->andWhere('p.nom LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['prix_min'])) {
            $qb->andWhere('p.prix >= :prix_min')
               ->setParameter('prix_min', $filters['prix_min']);
        }

        if (!empty($filters['prix_max'])) {
            $qb->andWhere('p.prix <= :prix_max')
               ->setParameter('prix_max', $filters['prix_max']);
        }

        if (!empty($filters['tri'])) {
            switch ($filters['tri']) {
                case 'prix_asc': $qb->orderBy('p.prix', 'ASC'); break;
                case 'prix_desc': $qb->orderBy('p.prix', 'DESC'); break;
                case 'nom_asc': $qb->orderBy('p.nom', 'ASC'); break;
                default: $qb->orderBy('p.id', 'DESC');
            }
        } else {
            $qb->orderBy('p.id', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }
}
