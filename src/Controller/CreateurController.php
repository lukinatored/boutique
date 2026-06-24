<?php
namespace App\Controller;

use App\Entity\Client;
use App\Entity\MontrePersonnalisee;
use App\Repository\MontrePersonnaliseeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/createur')]
class CreateurController extends AbstractController
{
    #[Route('/vitrine/{id}', name: 'app_createur_vitrine')]
    public function vitrine(int $id, MontrePersonnaliseeRepository $repository, EntityManagerInterface $em): Response
    {
        $createur = $em->getRepository(Client::class)->find($id);

        if (!$createur) {
            throw $this->createNotFoundException('Créateur non trouvé');
        }

        $montres = $repository->findBy([
            'createur' => $id,
            'estPubliee' => true
        ], ['createdAt' => 'DESC']);

        return $this->render('createur/vitrine.html.twig', [
            'createur' => $createur,
            'montres' => $montres
        ]);
    }

    #[Route('/dashboard', name: 'app_createur_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(MontrePersonnaliseeRepository $repository): Response
    {
        $user = $this->getUser();
        $montres = $repository->findBy(
            ['createur' => $user],
            ['createdAt' => 'DESC']
        );

        $publiees = array_filter($montres, function($m) { return $m->isEstPubliee(); });
        $enAttente = array_filter($montres, function($m) { return !$m->isEstPubliee(); });

        return $this->render('createur/dashboard.html.twig', [
            'montres' => $montres,
            'publiees' => count($publiees),
            'enAttente' => count($enAttente),
            'totalVues' => array_sum(array_map(function($m) { return $m->getNbVues(); }, $montres))
        ]);
    }

    #[Route('/liste', name: 'app_createur_liste')]
    public function liste(EntityManagerInterface $em): Response
    {
        $createurs = $em->getRepository(Client::class)
            ->createQueryBuilder('c')
            ->where('c.id IN (SELECT DISTINCT m.createur FROM App\Entity\MontrePersonnalisee m WHERE m.estPubliee = true)')
            ->getQuery()
            ->getResult();

        return $this->render('createur/liste.html.twig', [
            'createurs' => $createurs
        ]);
    }
}
