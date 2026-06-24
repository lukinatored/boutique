<?php
namespace App\Controller;

use App\Entity\Acheter;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Client;
use App\Entity\Notification;
use App\Repository\ProduitsRepository;
use App\Service\FideliteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    private EntityManagerInterface $em;
    private FideliteService $fideliteService;

    public function __construct(EntityManagerInterface $em, FideliteService $fideliteService)
    {
        $this->em = $em;
        $this->fideliteService = $fideliteService;
    }

    #[Route('/valider', name: 'app_commande_valider')]
    #[IsGranted('ROLE_USER')]
    public function valider(SessionInterface $session, ProduitsRepository $produitsRepository): Response
    {
        $panier = $session->get('panier', []);
        
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('app_cart');
        }

        $total = 0;
        $produitsCommande = [];
        
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
            $produitsCommande[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'prix' => $produit->getPrix()
            ];
        }

        $user = $this->getUser();

        // Créer la commande
        $commande = new Commande();
        $commande->setTotal($total);
        $commande->setClient($user);
        $commande->setStatut('en_attente');
        $this->em->persist($commande);

        // Créer les lignes de commande
        foreach ($produitsCommande as $item) {
            $acheter = new Acheter();
            $acheter->setProduit($item['produit']);
            $acheter->setCommande($commande);
            $acheter->setQuantite($item['quantite']);
            $acheter->setPrix($item['prix']);
            $this->em->persist($acheter);
            $item['produit']->setStock($item['produit']->getStock() - $item['quantite']);
        }

        // Créer la livraison
        $livraison = new Livraison();
        $livraison->setAdresse($user->getAdresse());
        $livraison->setCommande($commande);
        $livraison->setStatut('preparation');
        $this->em->persist($livraison);

        $this->em->flush();
        
        // Ajouter les points de fidélité
        $this->fideliteService->addPoints($user, $total);
        
        // Ajouter une notification
        $notification = new Notification();
        $notification->setClient($user);
        $notification->setMessage('✅ Votre commande #' . $commande->getId() . ' a été validée !');
        $notification->setType('commande');
        $this->em->persist($notification);
        $this->em->flush();
        
        $session->remove('panier');

        $this->addFlash('success', '✅ Commande validée ! Vous avez gagné ' . floor($total) . ' points de fidélité.');
        return $this->redirectToRoute('app_home');
    }
}
