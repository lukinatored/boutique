<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProduitRepository $montreRepository): Response
    {
        $panier = $session->get('panier', []);
        $montres = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $montre = $montreRepository->find($id);
            if ($montre) {
                $montres[] = ['montre' => $montre, 'quantite' => $quantite];
                $total += $montre->getPrix() * $quantite;
            }
        }

        return $this->render('panier/index.html.twig', [
            'montres' => $montres,
            'total' => $total,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter')]
    public function ajouter(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_boutique');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        unset($panier[$id]);
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }
}
