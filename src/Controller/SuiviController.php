<?php
namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/suivi')]
class SuiviController extends AbstractController
{
    #[Route('/{id}', name: 'app_suivi')]
    #[IsGranted('ROLE_USER')]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commande = $em->getRepository(Commande::class)->find($id);

        if (!$commande || $commande->getClient() !== $user) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Définir les étapes de suivi
        $etapes = [
            'en_attente' => [
                'label' => 'Commande validée',
                'icon' => 'fa-check-circle',
                'color' => 'text-warning',
                'date' => $commande->getCreatedAt()
            ],
            'expediee' => [
                'label' => 'Commande expédiée',
                'icon' => 'fa-truck',
                'color' => 'text-info',
                'date' => $commande->getDateExpedition()
            ],
            'en_livraison' => [
                'label' => 'En cours de livraison',
                'icon' => 'fa-shipping-fast',
                'color' => 'text-primary',
                'date' => $commande->getDateExpedition()
            ],
            'livree' => [
                'label' => 'Livrée !',
                'icon' => 'fa-check-circle',
                'color' => 'text-success',
                'date' => $commande->getDateLivraison()
            ]
        ];

        // Déterminer l'étape actuelle
        $statutActuel = $commande->getStatut();
        $etapesActives = [];
        $ordreEtapes = ['en_attente', 'expediee', 'en_livraison', 'livree'];
        $indexActuel = array_search($statutActuel, $ordreEtapes);

        foreach ($ordreEtapes as $index => $etape) {
            $etapesActives[$etape] = [
                'label' => $etapes[$etape]['label'],
                'icon' => $etapes[$etape]['icon'],
                'color' => $etapes[$etape]['color'],
                'date' => $etapes[$etape]['date'],
                'active' => $index <= $indexActuel,
                'current' => $index === $indexActuel
            ];
        }

        return $this->render('suivi/index.html.twig', [
            'commande' => $commande,
            'etapes' => $etapesActives,
            'statutActuel' => $statutActuel
        ]);
    }
}
