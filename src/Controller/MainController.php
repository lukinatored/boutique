<?php
namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        $produits = $produitsRepository->findLatest(8);
        return $this->render('main/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produits', name: 'app_produits')]
    public function produits(Request $request, ProduitsRepository $produitsRepository): Response
    {
        $search = $request->query->get('search');
        $prixMin = $request->query->get('prix_min');
        $prixMax = $request->query->get('prix_max');
        $tri = $request->query->get('tri');

        $filters = [];

        if ($search && $search !== '') {
            $filters['search'] = $search;
        }

        if ($prixMin && $prixMin !== '') {
            $filters['prix_min'] = (float)$prixMin;
        }

        if ($prixMax && $prixMax !== '') {
            $filters['prix_max'] = (float)$prixMax;
        }

        if ($tri && $tri !== '') {
            $filters['tri'] = $tri;
        }

        $produits = $produitsRepository->searchWithFilters($filters);

        return $this->render('main/produits.html.twig', [
            'produits' => $produits,
            'filters' => $filters,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_detail')]
    public function produitDetail(int $id, ProduitsRepository $produitsRepository): Response
    {
        $produit = $produitsRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        return $this->render('main/produit_detail.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/api/search', name: 'app_api_search')]
    public function apiSearch(Request $request, ProduitsRepository $produitsRepository): JsonResponse
    {
        $term = $request->query->get('q', '');
        
        if (strlen($term) < 2) {
            return $this->json(['results' => []]);
        }

        $results = $produitsRepository->searchAutocomplete($term);
        
        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result['id'],
                'nom' => $result['nom'],
                'prix' => $result['prix'],
                'stock' => $result['stock'],
                'url' => $this->generateUrl('app_produit_detail', ['id' => $result['id']]),
            ];
        }

        return $this->json(['results' => $data]);
    }

    #[Route('/recommandations', name: 'app_recommandations')]
    public function recommandations(ProduitsRepository $produitsRepository, Request $request): Response
    {
        $session = $request->getSession();
        $recentlyViewed = $session->get('recently_viewed', []);
        
        if (!empty($recentlyViewed)) {
            $produits = $produitsRepository->createQueryBuilder('p')
                ->where('p.id IN (:ids)')
                ->setParameter('ids', $recentlyViewed)
                ->setMaxResults(8)
                ->getQuery()
                ->getResult();
        } else {
            $produits = $produitsRepository->findBy([], ['id' => 'DESC'], 8);
        }
        
        return $this->render('main/recommandations.html.twig', [
            'produits' => $produits
        ]);
    }
}
