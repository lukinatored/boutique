<?php
namespace App\Controller;

use App\Entity\Wishlist;
use App\Repository\ProduitsRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wishlist')]
#[IsGranted('ROLE_USER')]
class WishlistController extends AbstractController
{
    #[Route('/', name: 'app_wishlist')]
    public function index(WishlistRepository $wishlistRepository): Response
    {
        $user = $this->getUser();
        $wishlist = $wishlistRepository->findBy(['client' => $user]);
        
        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_wishlist_add')]
    public function add(int $id, ProduitsRepository $produitsRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $produit = $produitsRepository->find($id);
        
        if (!$produit) {
            $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_home');
        }

        $existing = $em->getRepository(Wishlist::class)->findOneBy([
            'client' => $user,
            'produit' => $produit
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Déjà dans votre wishlist');
            return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
        }

        $wishlist = new Wishlist();
        $wishlist->setClient($user);
        $wishlist->setProduit($produit);

        $em->persist($wishlist);
        $em->flush();

        $this->addFlash('success', 'Produit ajouté à votre wishlist !');
        return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
    }

    #[Route('/supprimer/{id}', name: 'app_wishlist_remove')]
    public function remove(int $id, WishlistRepository $wishlistRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $wishlist = $wishlistRepository->findOneBy([
            'client' => $user,
            'produit' => $id
        ]);

        if (!$wishlist) {
            $this->addFlash('error', 'Produit non trouvé dans votre wishlist');
            return $this->redirectToRoute('app_wishlist');
        }

        $em->remove($wishlist);
        $em->flush();

        $this->addFlash('success', 'Produit retiré de votre wishlist');
        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/count', name: 'app_wishlist_count')]
    public function count(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $count = $em->getRepository(Wishlist::class)->count(['client' => $user]);
        return $this->json(['count' => $count]);
    }
}
