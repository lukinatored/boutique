<?php
namespace App\Service;

use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use App\Repository\AcheterRepository;
use App\Repository\AvisRepository;

class RecommendationService
{
    private $produitsRepository;
    private $acheterRepository;
    private $avisRepository;

    public function __construct(
        ProduitsRepository $produitsRepository,
        AcheterRepository $acheterRepository,
        AvisRepository $avisRepository
    ) {
        $this->produitsRepository = $produitsRepository;
        $this->acheterRepository = $acheterRepository;
        $this->avisRepository = $avisRepository;
    }

    public function getSimilarProducts(Produits $produit, int $limit = 4): array
    {
        $categories = [];
        if ($produit->getMontre() && $produit->getMontre()->getCategorie()) {
            $categories[] = $produit->getMontre()->getCategorie()->getId();
        }

        $query = $this->produitsRepository->createQueryBuilder('p')
            ->where('p.id != :id')
            ->setParameter('id', $produit->getId());

        if (!empty($categories)) {
            $query->leftJoin('p.montre', 'm')
                  ->leftJoin('m.categorie', 'c')
                  ->andWhere('c.id IN (:categories)')
                  ->setParameter('categories', $categories);
        }

        return $query->setMaxResults($limit)->getQuery()->getResult();
    }

    public function getBestSellers(int $limit = 4): array
    {
        try {
            $conn = $this->acheterRepository->getEntityManager()->getConnection();
            $sql = '
                SELECT p.id, p.nom, p.prix, p.stock, SUM(a.quantite) as total_vendus
                FROM produits p
                JOIN acheter a ON p.id = a.produit_id
                GROUP BY p.id
                ORDER BY total_vendus DESC
                LIMIT :limit
            ';
            
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
            $result = $stmt->executeQuery();
            $ids = $result->fetchAllAssociative();
            $produits = [];
            
            foreach ($ids as $item) {
                $produit = $this->produitsRepository->find($item['id']);
                if ($produit) {
                    $produits[] = $produit;
                }
            }
            
            return $produits;
        } catch (\Exception $e) {
            // Si la table acheter est vide, retourner des produits aléatoires
            return $this->getRandomProducts($limit);
        }
    }

    public function getRecentlyViewed(array $ids, int $limit = 4): array
    {
        if (empty($ids)) {
            return [];
        }
        
        return $this->produitsRepository->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getRandomProducts(int $limit = 4): array
    {
        return $this->produitsRepository->createQueryBuilder('p')
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getProductsByCategory(int $categorieId, int $limit = 4): array
    {
        return $this->produitsRepository->createQueryBuilder('p')
            ->leftJoin('p.montre', 'm')
            ->leftJoin('m.categorie', 'c')
            ->where('c.id = :categorieId')
            ->setParameter('categorieId', $categorieId)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
