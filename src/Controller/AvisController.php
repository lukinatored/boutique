<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Client;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AvisController extends AbstractController
{
    #[Route('/avis/ajouter/{id}', name: 'app_avis_ajouter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function ajouter(int $id, Request $request, ProduitsRepository $produitsRepository, EntityManagerInterface $em): Response
    {
        $produit = $produitsRepository->find($id);
        
        if (!$produit) {
            $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_produits');
        }
        
        $note = $request->get('note');
        $commentaire = $request->get('commentaire');
        
        if (!$note || $note < 1 || $note > 5) {
            $this->addFlash('error', 'Note invalide (1-5)');
            return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
        }
        
        /** @var Client $user */
        $user = $this->getUser();
        
        $avis = new Avis();
        $avis->setNote($note);
        $avis->setCommentaire($commentaire);
        $avis->setClient($user);
        $avis->setProduit($produit);
        
        $em->persist($avis);
        $em->flush();
        
        $this->addFlash('success', 'Votre avis a été ajouté');
        
        return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
    }
}
