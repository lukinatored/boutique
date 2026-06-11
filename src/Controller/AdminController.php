<?php
namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(
        ClientRepository $clientRepository,
        CommandeRepository $commandeRepository,
        ProduitsRepository $produitsRepository
    ): Response {
        $nbClients = count($clientRepository->findAll());
        $nbCommandes = count($commandeRepository->findAll());
        $nbProduits = count($produitsRepository->findAll());
        
        return $this->render('admin/dashboard.html.twig', [
            'nbClients' => $nbClients,
            'nbCommandes' => $nbCommandes,
            'nbProduits' => $nbProduits,
        ]);
    }
}
