<?php
namespace App\Controller;

use App\Entity\Acheter;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\CodePromo;
use App\Form\LivraisonType;
use App\Repository\ProduitsRepository;
use App\Repository\CodePromoRepository;
use App\Service\FideliteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/paiement-livraison')]
class PaiementLivraisonController extends AbstractController
{
    private FideliteService $fideliteService;

    public function __construct(FideliteService $fideliteService)
    {
        $this->fideliteService = $fideliteService;
    }

    #[Route('/formulaire', name: 'app_paiement_livraison_form')]
    #[IsGranted('ROLE_USER')]
    public function formulaire(Request $request, SessionInterface $session, ProduitsRepository $produitsRepository): Response
    {
        $panier = $session->get('panier', []);
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('app_cart');
        }

        // Calculer le total
        $total = 0;
        $items = [];
        foreach ($panier as $id => $quantite) {
            $produit = $produitsRepository->find($id);
            if ($produit) {
                $sousTotal = $produit->getPrix() * $quantite;
                $total += $sousTotal;
                $items[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'sousTotal' => $sousTotal
                ];
            }
        }

        // Créer le formulaire
        $form = $this->createForm(LivraisonType::class);
        $form->handleRequest($request);

        $codePromoMessage = null;

        if ($request->isMethod('POST')) {
            // Vérifier le code promo
            $code = $request->get('code_promo');
            if ($code) {
                $codePromo = $this->getDoctrine()->getRepository(CodePromo::class)->findValidCode($code);
                if ($codePromo) {
                    $reduction = $codePromo->getType() === 'pourcentage' 
                        ? $total * ($codePromo->getReduction() / 100) 
                        : $codePromo->getReduction();
                    $total -= $reduction;
                    $session->set('code_promo_applique', [
                        'code' => $code,
                        'reduction' => $reduction
                    ]);
                    $this->addFlash('success', '✅ Code promo appliqué !');
                } else {
                    $this->addFlash('error', '❌ Code promo invalide ou expiré');
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                
                // Sauvegarder les données de livraison en session
                $session->set('livraison_data', $data);
                
                return $this->redirectToRoute('app_paiement_livraison_recap');
            }
        }

        return $this->render('paiement/livraison_form.html.twig', [
            'form' => $form->createView(),
            'items' => $items,
            'total' => $total,
            'code_promo_applique' => $session->get('code_promo_applique')
        ]);
    }

    #[Route('/recapitulatif', name: 'app_paiement_livraison_recap')]
    #[IsGranted('ROLE_USER')]
    public function recapitulatif(SessionInterface $session, ProduitsRepository $produitsRepository, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);
        $livraisonData = $session->get('livraison_data');
        $codePromo = $session->get('code_promo_applique');

        if (empty($panier) || !$livraisonData) {
            return $this->redirectToRoute('app_paiement_livraison_form');
        }

        // Calculer le total
        $total = 0;
        $items = [];
        foreach ($panier as $id => $quantite) {
            $produit = $produitsRepository->find($id);
            if ($produit) {
                $sousTotal = $produit->getPrix() * $quantite;
                $total += $sousTotal;
                $items[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'sousTotal' => $sousTotal
                ];
            }
        }

        // Appliquer le code promo
        if ($codePromo) {
            $total -= $codePromo['reduction'];
        }

        return $this->render('paiement/livraison_recap.html.twig', [
            'items' => $items,
            'total' => $total,
            'livraison_data' => $livraisonData,
            'code_promo' => $codePromo
        ]);
    }

    #[Route('/confirmer', name: 'app_paiement_livraison_confirmer', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function confirmer(SessionInterface $session, ProduitsRepository $produitsRepository, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);
        $livraisonData = $session->get('livraison_data');
        $codePromo = $session->get('code_promo_applique');

        if (empty($panier) || !$livraisonData) {
            return $this->redirectToRoute('app_cart');
        }

        $user = $this->getUser();

        // Calculer le total
        $total = 0;
        $produitsCommande = [];
        foreach ($panier as $id => $quantite) {
            $produit = $produitsRepository->find($id);
            if (!$produit || $produit->getStock() < $quantite) {
                $this->addFlash('error', 'Stock insuffisant');
                return $this->redirectToRoute('app_cart');
            }
            $sousTotal = $produit->getPrix() * $quantite;
            $total += $sousTotal;
            $produitsCommande[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'prix' => $produit->getPrix()
            ];
        }

        // Appliquer le code promo
        if ($codePromo) {
            $total -= $codePromo['reduction'];
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setTotal(max(0, $total));
        $commande->setClient($user);
        $commande->setStatut('en_attente');
        $em->persist($commande);

        // Créer les lignes de commande
        foreach ($produitsCommande as $item) {
            $acheter = new Acheter();
            $acheter->setProduit($item['produit']);
            $acheter->setCommande($commande);
            $acheter->setQuantite($item['quantite']);
            $acheter->setPrix($item['prix']);
            $em->persist($acheter);
            $item['produit']->setStock($item['produit']->getStock() - $item['quantite']);
        }

        // Créer la livraison
        $livraison = new Livraison();
        $livraison->setAdresse(
            $livraisonData['adresse'] . "\n" .
            $livraisonData['codePostal'] . ' ' . $livraisonData['ville'] . "\n" .
            'Département: ' . $livraisonData['departement'] . "\n" .
            'Région: ' . $livraisonData['region']
        );
        $livraison->setCommande($commande);
        $livraison->setStatut('preparation');
        $em->persist($livraison);

        $em->flush();

        // Ajouter les points de fidélité
        $this->fideliteService->addPoints($user, $total);

        // Vider le panier
        $session->remove('panier');
        $session->remove('livraison_data');
        $session->remove('code_promo_applique');

        $this->addFlash('success', '✅ Commande validée avec succès !');

        return $this->redirectToRoute('app_suivi', ['id' => $commande->getId()]);
    }
}
