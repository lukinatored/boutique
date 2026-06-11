<?php
namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(SessionInterface $session, ProduitsRepository $produitsRepository): Response
    {
        $panier = $session->get('panier', []);
        $items = [];
        $total = 0;
        
        foreach ($panier as $id => $quantite) {
            $produit = $produitsRepository->find($id);
            if ($produit) {
                $items[] = [
                    'produit' => $produit,
                    'quantite' => $quantite
                ];
                $total += $produit->getPrix() * $quantite;
            }
        }
        
        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'total' => $total
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_cart_add')]
    public function add(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        
        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        
        $session->set('panier', $panier);
        $this->addFlash('success', 'Produit ajouté au panier');
        
        return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
    }

    #[Route('/supprimer/{id}', name: 'app_cart_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        
        if (isset($panier[$id])) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('success', 'Produit supprimé du panier');
        }
        
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/vider', name: 'app_cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('panier');
        $this->addFlash('success', 'Panier vidé');
        
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/count', name: 'app_cart_count')]
    public function count(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $count = array_sum($panier);
        
        return $this->json(['count' => $count]);
    }
}
