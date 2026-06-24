<?php
namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/livraison')]
class LivraisonSimulationController extends AbstractController
{
    #[Route('/simuler/{id}', name: 'app_livraison_simuler')]
    #[IsGranted('ROLE_USER')]
    public function simuler(int $id, EntityManagerInterface $em): Response
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        $user = $this->getUser();

        if (!$commande || $commande->getClient() !== $user) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Initialiser l'historique si vide
        if (empty($commande->getHistorique())) {
            $this->initHistorique($commande);
            $em->flush();
        }

        // Générer des notifications simulées
        $notifications = $this->getNotifications($commande);

        return $this->render('livraison/simulation.html.twig', [
            'commande' => $commande,
            'etapes' => $this->getEtapes($commande),
            'notifications' => $notifications,
        ]);
    }

    #[Route('/avancer/{id}', name: 'app_livraison_avancer')]
    #[IsGranted('ROLE_USER')]
    public function avancer(int $id, EntityManagerInterface $em): JsonResponse
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        $user = $this->getUser();

        if (!$commande || $commande->getClient() !== $user) {
            return $this->json(['error' => 'Commande non trouvée'], 404);
        }

        $statutActuel = $commande->getStatut();
        $prochainStatut = $this->getProchainStatut($statutActuel);

        if (!$prochainStatut) {
            return $this->json(['error' => 'Déjà livré'], 400);
        }

        $commande->setStatut($prochainStatut);
        
        // Ajouter des notifications selon le statut
        if ($prochainStatut === 'expediee') {
            $commande->setDateExpedition(new \DateTime());
            $commande->setNumeroSuivi('LX' . strtoupper(uniqid()));
            $commande->addHistorique('expediee', '📦 Votre commande a été expédiée !');
        } elseif ($prochainStatut === 'en_livraison') {
            $commande->addHistorique('en_livraison', '🚚 Votre commande est en cours de livraison, arrivée prévue dans 2 heures');
        } elseif ($prochainStatut === 'livree') {
            $commande->setDateLivraison(new \DateTime());
            $commande->addHistorique('livree', '✅ Votre commande a été livrée avec succès !');
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'statut' => $prochainStatut,
            'message' => $this->getMessageStatus($prochainStatut),
            'notifications' => $this->getNotifications($commande)
        ]);
    }

    private function getMessageStatus(string $statut): string
    {
        $messages = [
            'en_attente' => '⏳ Commande en attente de validation',
            'validee' => '✅ Commande validée',
            'expediee' => '📦 Commande expédiée !',
            'en_livraison' => '🚚 Commande en cours de livraison (arrivée dans 2h)',
            'livree' => '🏠 Commande livrée avec succès !'
        ];
        return $messages[$statut] ?? 'Statut inconnu';
    }

    private function getNotifications(Commande $commande): array
    {
        $notifications = [];
        $statut = $commande->getStatut();

        if ($statut === 'expediee') {
            $notifications[] = [
                'icon' => '📦',
                'message' => 'Votre commande a été expédiée !',
                'time' => 'Maintenant',
                'type' => 'success'
            ];
            $notifications[] = [
                'icon' => '📋',
                'message' => 'Le colis est en cours de préparation chez le vendeur',
                'time' => 'Il y a 1h',
                'type' => 'info'
            ];
            $notifications[] = [
                'icon' => '✅',
                'message' => 'Votre commande a été validée par le vendeur',
                'time' => 'Il y a 2h',
                'type' => 'success'
            ];
        } elseif ($statut === 'en_livraison') {
            $notifications[] = [
                'icon' => '🚚',
                'message' => '🚚 Votre colis est en route ! Arrivée prévue dans 2 heures',
                'time' => 'Maintenant',
                'type' => 'warning'
            ];
            $notifications[] = [
                'icon' => '📍',
                'message' => 'Votre colis est au centre de tri de Paris',
                'time' => 'Il y a 30min',
                'type' => 'info'
            ];
            $notifications[] = [
                'icon' => '📦',
                'message' => 'Votre colis a quitté l\'entrepôt',
                'time' => 'Il y a 1h',
                'type' => 'success'
            ];
            $notifications[] = [
                'icon' => '📋',
                'message' => 'Votre commande a été expédiée',
                'time' => 'Il y a 2h',
                'type' => 'info'
            ];
        } elseif ($statut === 'livree') {
            $notifications[] = [
                'icon' => '🏠',
                'message' => '🎉 Votre commande a été livrée avec succès !',
                'time' => 'Maintenant',
                'type' => 'success'
            ];
            $notifications[] = [
                'icon' => '🚚',
                'message' => 'Votre colis est arrivé au point de livraison',
                'time' => 'Il y a 30min',
                'type' => 'info'
            ];
            $notifications[] = [
                'icon' => '📍',
                'message' => 'Votre colis est en cours de livraison',
                'time' => 'Il y a 1h',
                'type' => 'info'
            ];
            $notifications[] = [
                'icon' => '📦',
                'message' => 'Votre colis a quitté le centre de tri',
                'time' => 'Il y a 2h',
                'type' => 'success'
            ];
        } else {
            $notifications[] = [
                'icon' => '⏳',
                'message' => 'Votre commande est en attente de validation',
                'time' => 'Maintenant',
                'type' => 'info'
            ];
        }

        return $notifications;
    }

    private function initHistorique(Commande $commande): void
    {
        $commande->addHistorique('en_attente', '🕐 Commande en attente de validation');
        $commande->setStatut('en_attente');
    }

    private function getEtapes(Commande $commande): array
    {
        $etapes = [
            'en_attente' => [
                'label' => 'En attente',
                'icon' => '⏳',
                'active' => true
            ],
            'validee' => [
                'label' => 'Validée',
                'icon' => '✅',
                'active' => false
            ],
            'expediee' => [
                'label' => 'Expédiée',
                'icon' => '📦',
                'active' => false
            ],
            'en_livraison' => [
                'label' => 'En livraison',
                'icon' => '🚚',
                'active' => false
            ],
            'livree' => [
                'label' => 'Livrée',
                'icon' => '🏠',
                'active' => false
            ]
        ];

        $statutActuel = $commande->getStatut();
        $ordre = ['en_attente', 'validee', 'expediee', 'en_livraison', 'livree'];
        $indexActuel = array_search($statutActuel, $ordre);

        foreach ($etapes as $key => &$etape) {
            $indexEtape = array_search($key, $ordre);
            $etape['active'] = $indexEtape <= $indexActuel;
            $etape['current'] = $indexEtape === $indexActuel;
        }

        return $etapes;
    }

    private function getProchainStatut(string $statutActuel): ?string
    {
        $ordre = ['en_attente', 'validee', 'expediee', 'en_livraison', 'livree'];
        $index = array_search($statutActuel, $ordre);
        
        if ($index !== false && isset($ordre[$index + 1])) {
            return $ordre[$index + 1];
        }
        
        return null;
    }
}
