<?php
namespace App\Controller;

use App\Repository\CodePromoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CodePromoController extends AbstractController
{
    #[Route('/code-promo/valider', name: 'app_code_promo_valider', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function valider(Request $request, CodePromoRepository $codePromoRepository): Response
    {
        $code = $request->get('code');
        $total = $request->get('total');
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app_cart');
        }

        $codePromo = $codePromoRepository->findValidCode($code);

        if (!$codePromo) {
            $this->addFlash('error', 'Code promo invalide ou expiré');
            return $this->redirectToRoute('app_cart');
        }

        // Calculer la réduction
        $reduction = 0;
        if ($codePromo->getType() === 'pourcentage') {
            $reduction = $total * ($codePromo->getReduction() / 100);
        } else {
            $reduction = $codePromo->getReduction();
        }

        // Appliquer la réduction
        $session->set('code_promo', [
            'code' => $code,
            'reduction' => $reduction
        ]);

        $this->addFlash('success', 'Code promo appliqué ! Réduction de ' . $reduction . ' €');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/code-promo/supprimer', name: 'app_code_promo_remove')]
    #[IsGranted('ROLE_USER')]
    public function remove(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('code_promo');
        
        $this->addFlash('success', 'Code promo supprimé');
        return $this->redirectToRoute('app_cart');
    }
}
