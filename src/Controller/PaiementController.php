<?php
namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/paiement')]
class PaiementController extends AbstractController
{
    #[Route('/stripe', name: 'app_paiement_stripe')]
    #[IsGranted('ROLE_USER')]
    public function stripe(Request $request, SessionInterface $session, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);
        
        if (empty($panier)) {
            $this->addFlash('warning', 'Panier vide');
            return $this->redirectToRoute('app_cart');
        }

        $mode = $request->get('mode', 'carte');

        // Si paiement à la livraison
        if ($mode === 'livraison') {
            return $this->redirectToRoute('app_commande_valider');
        }

        // Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];

        foreach ($panier as $id => $quantite) {
            $produit = $em->getRepository(\App\Entity\Produits::class)->find($id);
            if ($produit) {
                $prix = $produit->getPrix();
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $produit->getNom()],
                        'unit_amount' => intval($prix * 100),
                    ],
                    'quantity' => $quantite,
                ];
            }
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_paiement_success', [], 0),
            'cancel_url' => $this->generateUrl('app_paiement_cancel', [], 0),
        ]);

        return $this->redirect($checkoutSession->url);
    }

    #[Route('/success', name: 'app_paiement_success')]
    public function success(Request $request, SessionInterface $session): Response
    {
        $session->remove('panier');
        $this->addFlash('success', '✅ Paiement effectué avec succès !');
        return $this->render('paiement/success.html.twig');
    }

    #[Route('/cancel', name: 'app_paiement_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('error', '❌ Paiement annulé');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/choisir', name: 'app_paiement_choisir')]
    #[IsGranted('ROLE_USER')]
    public function choisir(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        if (empty($panier)) {
            return $this->redirectToRoute('app_cart');
        }
        return $this->render('paiement/choisir.html.twig');
    }
}
