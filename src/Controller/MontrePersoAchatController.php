<?php
namespace App\Controller;

use App\Entity\MontrePersonnalisee;
use App\Entity\Commande;
use App\Entity\Acheter;
use App\Entity\Livraison;
use App\Entity\Client;
use App\Repository\MontrePersonnaliseeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/montre-personnalisee/achat')]
class MontrePersoAchatController extends AbstractController
{
    #[Route('/panier/ajouter/{id}', name: 'app_montre_perso_achat_ajouter')]
    #[IsGranted('ROLE_USER')]
    public function ajouterPanier(int $id, MontrePersonnaliseeRepository $repository, SessionInterface $session): Response
    {
        $montre = $repository->find($id);
        
        if (!$montre || !$montre->isEstPubliee()) {
            $this->addFlash('error', 'Montre non disponible');
            return $this->redirectToRoute('app_montre_perso');
        }

        if ($montre->getStock() <= 0) {
            $this->addFlash('error', 'Cette montre n\'est plus en stock');
            return $this->redirectToRoute('app_montre_perso_detail', ['id' => $id]);
        }

        // Ajouter au panier
        $panierPerso = $session->get('panier_perso', []);
        
        if (isset($panierPerso[$id])) {
            $panierPerso[$id] = min($panierPerso[$id] + 1, $montre->getStock());
        } else {
            $panierPerso[$id] = 1;
        }
        
        $session->set('panier_perso', $panierPerso);
        
        $this->addFlash('success', 'Montre ajoutée à votre panier personnalisé !');
        return $this->redirectToRoute('app_montre_perso_detail', ['id' => $id]);
    }

    #[Route('/panier', name: 'app_montre_perso_achat_panier')]
    #[IsGranted('ROLE_USER')]
    public function panier(SessionInterface $session, MontrePersonnaliseeRepository $repository): Response
    {
        $panierPerso = $session->get('panier_perso', []);
        $items = [];
        $total = 0;
        
        foreach ($panierPerso as $id => $quantite) {
            $montre = $repository->find($id);
            if ($montre && $montre->isEstPubliee() && $montre->getStock() > 0) {
                $sousTotal = $montre->getPrix() * $quantite;
                $total += $sousTotal;
                $items[] = [
                    'montre' => $montre,
                    'quantite' => $quantite,
                    'sousTotal' => $sousTotal
                ];
            }
        }
        
        return $this->render('montre_perso/panier.html.twig', [
            'items' => $items,
            'total' => $total
        ]);
    }

    #[Route('/panier/supprimer/{id}', name: 'app_montre_perso_achat_supprimer')]
    #[IsGranted('ROLE_USER')]
    public function supprimerPanier(int $id, SessionInterface $session): Response
    {
        $panierPerso = $session->get('panier_perso', []);
        unset($panierPerso[$id]);
        $session->set('panier_perso', $panierPerso);
        
        $this->addFlash('success', 'Montre retirée du panier');
        return $this->redirectToRoute('app_montre_perso_achat_panier');
    }

    #[Route('/valider', name: 'app_montre_perso_achat_valider')]
    #[IsGranted('ROLE_USER')]
    public function valider(SessionInterface $session, MontrePersonnaliseeRepository $repository, EntityManagerInterface $em): Response
    {
        $panierPerso = $session->get('panier_perso', []);
        
        if (empty($panierPerso)) {
            $this->addFlash('warning', 'Votre panier personnalisé est vide');
            return $this->redirectToRoute('app_montre_perso_achat_panier');
        }

        $total = 0;
        $items = [];
        
        foreach ($panierPerso as $id => $quantite) {
            $montre = $repository->find($id);
            if (!$montre || !$montre->isEstPubliee() || $montre->getStock() < $quantite) {
                $this->addFlash('error', 'Stock insuffisant pour ' . ($montre ? $montre->getNom() : 'une montre'));
                return $this->redirectToRoute('app_montre_perso_achat_panier');
            }
            $sousTotal = $montre->getPrix() * $quantite;
            $total += $sousTotal;
            $items[] = [
                'montre' => $montre,
                'quantite' => $quantite,
                'prix' => $montre->getPrix()
            ];
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setTotal($total);
        $commande->setClient($this->getUser());
        $commande->setStatut('en_attente');
        $em->persist($commande);

        // Créer les lignes de commande
        foreach ($items as $item) {
            $acheter = new Acheter();
            $acheter->setProduit(null); // Pas de produit standard
            $acheter->setCommande($commande);
            $acheter->setQuantite($item['quantite']);
            $acheter->setPrix($item['prix']);
            $em->persist($acheter);
            
            // Mettre à jour le stock du vendeur
            $montre = $item['montre'];
            $montre->setStock($montre->getStock() - $item['quantite']);
            $montre->incrementVendus();
        }

        // Créer la livraison
        $livraison = new Livraison();
        $livraison->setAdresse($this->getUser()->getAdresse());
        $livraison->setCommande($commande);
        $livraison->setStatut('preparation');
        $em->persist($livraison);

        $em->flush();
        
        // Vider le panier
        $session->remove('panier_perso');

        $this->addFlash('success', 'Commande validée avec succès !');
        return $this->redirectToRoute('app_home');
    }
}
