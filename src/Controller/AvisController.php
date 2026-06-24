<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/avis')]
class AvisController extends AbstractController
{
    #[Route('/ajouter/{id}', name: 'app_avis_ajouter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function ajouter(int $id, Request $request, ProduitsRepository $produitsRepository, EntityManagerInterface $em): Response
    {
        $produit = $produitsRepository->find($id);
        if (!$produit) {
            $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
        }

        $note = $request->get('note');
        $commentaire = trim($request->get('commentaire'));

        if (!$note || $note < 1 || $note > 5) {
            $this->addFlash('error', 'Note invalide (1-5)');
            return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
        }

        if (empty($commentaire)) {
            $this->addFlash('error', 'Le commentaire est obligatoire');
            return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
        }

        $avis = new Avis();
        $avis->setProduit($produit);
        $avis->setClient($this->getUser());
        $avis->setNote($note);
        $avis->setCommentaire($commentaire);

        $em->persist($avis);
        $em->flush();

        $this->addFlash('success', 'Votre avis a été ajouté avec succès !');
        return $this->redirectToRoute('app_produit_detail', ['id' => $id]);
    }
}
