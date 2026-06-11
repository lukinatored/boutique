<?php
namespace App\Controller;

use App\Entity\Acheter;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/valider', name: 'app_commande_valider')]
    #[IsGranted('ROLE_USER')]
    public function valider(SessionInterface $session, ProduitsRepository $produitsRepository, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);
        
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('app_cart');
        }

        $total = 0;
        foreach ($panier as $id => $quantite) {
            $produit = $produitsRepository->find($id);
            if (!$produit) {
                $this->addFlash('error', 'Produit non trouvé');
                return $this->redirectToRoute('app_cart');
            }
            if ($produit->getStock() < $quantite) {
                $this->addFlash('error', 'Stock insuffisant pour ' . $produit->getNom());
                return $this->redirectToRoute('app_cart');
            }
            $total += $produit->getPrix() * $quantite;
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setTotal($total);
        $commande->setClient($this->getUser());
        $commande->setStatut('en_attente');
        $em->persist($commande);

        // Créer les lignes de commande
        foreach ($panier as $id => $quantite) {
            $produit = $produitsRepository->find($id);
            
            $acheter = new Acheter();
            $acheter->setProduit($produit);
            $acheter->setCommande($commande);
            $acheter->setQuantite($quantite);
            $acheter->setPrix($produit->getPrix());
            $em->persist($acheter);
            
            // Mettre à jour le stock
            $produit->setStock($produit->getStock() - $quantite);
        }

        // Créer la livraison
        $livraison = new Livraison();
        $livraison->setAdresse($this->getUser()->getAdresse());
        $livraison->setCommande($commande);
        $livraison->setStatut('preparation');
        $em->persist($livraison);

        $em->flush();
        
        // Vider le panier
        $session->remove('panier');

        $this->addFlash('success', 'Votre commande a été validée avec succès !');
        return $this->redirectToRoute('app_home');
    }
}
