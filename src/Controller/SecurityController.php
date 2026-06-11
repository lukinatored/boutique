<?php
namespace App\Controller;

use App\Entity\Client;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $prenom = $request->get('prenom');
            $email = $request->get('email');
            $adresse = $request->get('adresse');
            $password = $request->get('password');
            $confirmPassword = $request->get('confirm_password');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('app_register');
            }

            $existingUser = $em->getRepository(Client::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé');
                return $this->redirectToRoute('app_register');
            }

            $role = $em->getRepository(Role::class)->findOneBy(['nom' => 'ROLE_USER']);
            if (!$role) {
                $role = new Role();
                $role->setNom('ROLE_USER');
                $em->persist($role);
                $em->flush();
            }

            $user = new Client();
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setAdresse($adresse);
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setRole($role);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig');
    }
}
