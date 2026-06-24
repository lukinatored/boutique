<?php
namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/export')]
#[IsGranted('ROLE_ADMIN')]
class ExportController extends AbstractController
{
    #[Route('/commandes', name: 'app_export_commandes')]
    public function exportCommandes(EntityManagerInterface $em): Response
    {
        $commandes = $em->getRepository(Commande::class)->findAll();

        $csv = "ID,Client,Total,Statut,Date\n";
        foreach ($commandes as $commande) {
            $csv .= sprintf(
                "%d,%s,%.2f,%s,%s\n",
                $commande->getId(),
                $commande->getClient()->getEmail(),
                $commande->getTotal(),
                $commande->getStatut(),
                $commande->getCreatedAt()->format('d/m/Y H:i')
            );
        }

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="commandes.csv"');

        return $response;
    }
}
