<?php
namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Commande;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/facture')]
class FactureController extends AbstractController
{
    #[Route('/generer/{commandeId}', name: 'app_facture_generer')]
    #[IsGranted('ROLE_USER')]
    public function generer(int $commandeId, EntityManagerInterface $em): Response
    {
        $commande = $em->getRepository(Commande::class)->find($commandeId);
        $user = $this->getUser();

        if (!$commande || $commande->getClient() !== $user) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Vérifier si la facture existe déjà
        $factureExistante = $em->getRepository(Facture::class)->findOneBy(['commande' => $commande]);
        
        if ($factureExistante) {
            return $this->redirectToRoute('app_facture_voir', ['id' => $factureExistante->getId()]);
        }

        // Créer la facture
        $facture = new Facture();
        $facture->setCommande($commande);
        $facture->setClient($user);
        
        // Utiliser un point pour les décimales
        $total = floatval($commande->getTotal());
        $facture->setTotal(number_format($total, 2, '.', ''));
        
        $tva = $total * 0.20;
        $facture->setTva(number_format($tva, 2, '.', ''));
        $facture->setTotalTTC(number_format($total + $tva, 2, '.', ''));
        
        // Générer le numéro de facture
        $year = date('Y');
        $count = $em->getRepository(Facture::class)->count([]) + 1;
        $facture->setNumero('FAC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT));

        $em->persist($facture);
        $em->flush();

        $this->addFlash('success', 'Facture générée avec succès !');
        return $this->redirectToRoute('app_facture_voir', ['id' => $facture->getId()]);
    }

    #[Route('/voir/{id}', name: 'app_facture_voir')]
    #[IsGranted('ROLE_USER')]
    public function voir(int $id, EntityManagerInterface $em): Response
    {
        $facture = $em->getRepository(Facture::class)->find($id);
        $user = $this->getUser();

        if (!$facture || $facture->getClient() !== $user) {
            throw $this->createNotFoundException('Facture non trouvée');
        }

        return $this->render('facture/voir.html.twig', [
            'facture' => $facture
        ]);
    }

    #[Route('/pdf/{id}', name: 'app_facture_pdf')]
    #[IsGranted('ROLE_USER')]
    public function pdf(int $id, EntityManagerInterface $em, PdfService $pdfService): Response
    {
        $facture = $em->getRepository(Facture::class)->find($id);
        $user = $this->getUser();

        if (!$facture || $facture->getClient() !== $user) {
            throw $this->createNotFoundException('Facture non trouvée');
        }

        $pdfContent = $pdfService->generateFacturePdf($facture);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="facture_' . $facture->getNumero() . '.pdf"');

        return $response;
    }

    #[Route('/admin/liste', name: 'app_facture_admin_liste')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminListe(EntityManagerInterface $em): Response
    {
        $factures = $em->getRepository(Facture::class)->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('facture/admin_liste.html.twig', [
            'factures' => $factures
        ]);
    }
}
