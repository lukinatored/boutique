<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Repository\MontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(MontreRepository $montreRepository): Response
    {
        $montres = $montreRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'montres' => $montres,
        ]);
    }
    #[Route('/boutique/{id}', name: 'app_boutique_detail')]
public function detail(Montre $montre): Response
{
    return $this->render('boutique/detail.html.twig', [
        'montre' => $montre,
    ]);
}

}
=======
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/acceuil', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('accueil.html.twig');
    }

    #[Route('/boutique', name: 'app_boutique')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/boutique/{id}', name: 'app_boutique_detail')]
    public function detail(ProduitRepository $produitRepository, int $id): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('boutique/detail.html.twig', [
            'produit' => $produit,
        ]);
    }
}
>>>>>>> 43fbb94 (Initial project Symfony boutique)
