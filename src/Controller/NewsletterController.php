<?php
namespace App\Controller;

use App\Entity\Newsletter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'app_newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em): Response
    {
        $email = $request->get('email');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Email invalide');
            return $this->redirectToRoute('app_home');
        }

        $existing = $em->getRepository(Newsletter::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à la newsletter');
            return $this->redirectToRoute('app_home');
        }

        $newsletter = new Newsletter();
        $newsletter->setEmail($email);
        $em->persist($newsletter);
        $em->flush();

        $this->addFlash('success', 'Inscription à la newsletter réussie !');
        return $this->redirectToRoute('app_home');
    }
}
