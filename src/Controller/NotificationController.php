<?php
namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notifications')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $notifications = $em->getRepository(Notification::class)
            ->findBy(['client' => $user], ['createdAt' => 'DESC']);

        foreach ($notifications as $notif) {
            if (!$notif->isLu()) {
                $notif->setLu(true);
            }
        }
        $em->flush();

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/count', name: 'app_notifications_count')]
    public function count(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $count = $em->getRepository(Notification::class)->count([
            'client' => $user,
            'lu' => false
        ]);
        
        return $this->json(['count' => $count]);
    }

    #[Route('/mark-read/{id}', name: 'app_notifications_mark_read')]
    public function markRead(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $notification = $em->getRepository(Notification::class)->find($id);

        if ($notification && $notification->getClient() === $user) {
            $notification->setLu(true);
            $em->flush();
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }
}
