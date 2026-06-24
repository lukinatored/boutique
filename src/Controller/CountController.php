<?php
namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CountController extends AbstractController
{
    #[Route('/panier/count', name: 'app_cart_count')]
    public function cartCount(SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        $count = array_sum($panier);
        return $this->json(['count' => $count]);
    }

    #[Route('/wishlist/count', name: 'app_wishlist_count')]
    public function wishlistCount(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['count' => 0]);
        }
        $count = $em->getRepository(Wishlist::class)->count(['client' => $user]);
        return $this->json(['count' => $count]);
    }

    #[Route('/notifications/count', name: 'app_notifications_count')]
    public function notificationsCount(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['count' => 0]);
        }
        $count = $em->getRepository(Notification::class)->count([
            'client' => $user,
            'lu' => false
        ]);
        return $this->json(['count' => $count]);
    }
}
