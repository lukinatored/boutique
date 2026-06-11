<?php
namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        $produits = $produitsRepository->findBy([], ['id' => 'DESC'], 8);
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

        $qb = $produitsRepository->createQueryBuilder('p');

        if ($search) {
            $qb->andWhere('p.nom LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($prixMin) {
            $qb->andWhere('p.prix >= :prixMin')->setParameter('prixMin', $prixMin);
        }

        if ($prixMax) {
            $qb->andWhere('p.prix <= :prixMax')->setParameter('prixMax', $prixMax);
        }

        if ($tri === 'prix_asc') {
            $qb->orderBy('p.prix', 'ASC');
        } elseif ($tri === 'prix_desc') {
            $qb->orderBy('p.prix', 'DESC');
        } elseif ($tri === 'nom_asc') {
            $qb->orderBy('p.nom', 'ASC');
        } else {
            $qb->orderBy('p.id', 'DESC');
        }

        $produits = $qb->getQuery()->getResult();

        return $this->render('main/produits.html.twig', [
            'produits' => $produits,
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
}
