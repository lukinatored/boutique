<?php
namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/compte')]
#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        /** @var Client $user */
        $user = $this->getUser();
        
        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/modifier', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Client $user */
        $user = $this->getUser();
        
        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $prenom = $request->get('prenom');
            $adresse = $request->get('adresse');
            
            if ($nom) $user->setNom($nom);
            if ($prenom) $user->setPrenom($prenom);
            if ($adresse) $user->setAdresse($adresse);
            
            $em->flush();
            
            $this->addFlash('success', 'Vos informations ont été mises à jour');
            return $this->redirectToRoute('app_account');
        }
        
        return $this->render('account/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/changer-mot-de-passe', name: 'app_account_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        /** @var Client $user */
        $user = $this->getUser();
        
        if ($request->isMethod('POST')) {
            $oldPassword = $request->get('old_password');
            $newPassword = $request->get('new_password');
            $confirmPassword = $request->get('confirm_password');
            
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', 'Ancien mot de passe incorrect');
                return $this->redirectToRoute('app_account_change_password');
            }
            
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas');
                return $this->redirectToRoute('app_account_change_password');
            }
            
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $em->flush();
            
            $this->addFlash('success', 'Votre mot de passe a été modifié');
            return $this->redirectToRoute('app_account');
        }
        
        return $this->render('account/change_password.html.twig');
    }
}
