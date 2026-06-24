<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produits;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        $nbClients = $em->getRepository(Client::class)->count([]);
        $nbCommandes = $em->getRepository(Commande::class)->count([]);
        $nbProduits = $em->getRepository(Produits::class)->count([]);
        
        $ca = $em->createQueryBuilder()
            ->select('SUM(c.total)')
            ->from(Commande::class, 'c')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
        $stockBas = $em->getRepository(Produits::class)->findBy(['stock' => [0,1,2,3,4]]);
        
        $dernieresCommandes = $em->getRepository(Commande::class)->findBy(
            [], 
            ['id' => 'DESC'], 
            5
        );
        
        return $this->render('admin/dashboard.html.twig', [
            'nbClients' => $nbClients,
            'nbCommandes' => $nbCommandes,
            'nbProduits' => $nbProduits,
            'ca' => $ca,
            'stockBas' => $stockBas,
            'dernieresCommandes' => $dernieresCommandes,
        ]);
    }

    #[Route('/stock/alerte', name: 'app_admin_stock_alerte')]
    public function stockAlerte(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produits::class)->findBy(['stock' => [0,1,2,3,4]]);
        
        return $this->render('admin/stock_alerte.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/stats', name: 'app_admin_stats')]
    public function stats(EntityManagerInterface $em): Response
    {
        $totalCommandes = $em->getRepository(Commande::class)->count([]);
        $totalProduits = $em->getRepository(Produits::class)->count([]);
        $totalClients = $em->getRepository(Client::class)->count([]);
        
        $ca = $em->createQueryBuilder()
            ->select('SUM(c.total)')
            ->from(Commande::class, 'c')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
        return $this->render('admin/stats.html.twig', [
            'totalCommandes' => $totalCommandes,
            'totalProduits' => $totalProduits,
            'totalClients' => $totalClients,
            'ca' => $ca,
        ]);
    }
}
