<?php
namespace App\Controller;

use App\Repository\MontrePersonnaliseeRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vendeur')]
#[IsGranted('ROLE_USER')]
class VendeurController extends AbstractController
{
    #[Route('/', name: 'app_vendeur_dashboard')]
    public function dashboard(MontrePersonnaliseeRepository $montreRepository, CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $montres = $montreRepository->findByCreateur($user->getId());
        
        $totalVendus = 0;
        $totalStock = 0;
        $totalVues = 0;
        
        foreach ($montres as $montre) {
            $totalVendus += $montre->getVendus();
            $totalStock += $montre->getStock();
            $totalVues += $montre->getNbVues();
        }
        
        return $this->render('vendeur/dashboard.html.twig', [
            'montres' => $montres,
            'totalVendus' => $totalVendus,
            'totalStock' => $totalStock,
            'totalVues' => $totalVues,
            'nbMontres' => count($montres)
        ]);
    }
}
