<?php
namespace App\Controller;

use App\Entity\MontrePersonnalisee;
use App\Repository\MontrePersonnaliseeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/montre-personnalisee')]
class MontrePersoController extends AbstractController
{
    #[Route('/', name: 'app_montre_perso')]
    public function index(MontrePersonnaliseeRepository $repository): Response
    {
        $montres = $repository->findPubliees();
        
        return $this->render('montre_perso/index.html.twig', [
            'montres' => $montres
        ]);
    }

    #[Route('/creer', name: 'app_montre_perso_creer')]
    #[IsGranted('ROLE_USER')]
    public function creer(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            // DEBUG: Afficher toutes les données reçues
            dump($request->request->all());
            dump($request->files->all());

            // Récupérer les données
            $nom = trim($request->request->get('nom', ''));
            $description = trim($request->request->get('description', ''));
            $boitier = $request->request->get('boitier', '');
            $bracelet = $request->request->get('bracelet', '');
            $mouvement = $request->request->get('mouvement', '');
            $prix = $request->request->get('prix', '');
            $source = trim($request->request->get('source', ''));

            // DEBUG: Afficher les valeurs
            dump([
                'nom' => $nom,
                'description' => $description,
                'boitier' => $boitier,
                'bracelet' => $bracelet,
                'mouvement' => $mouvement,
                'prix' => $prix,
                'source' => $source
            ]);

            // Valider les champs obligatoires
            if (empty($nom)) {
                $this->addFlash('error', 'Le nom de la montre est obligatoire');
                return $this->redirectToRoute('app_montre_perso_creer');
            }

            if (empty($description)) {
                $this->addFlash('error', 'La description est obligatoire');
                return $this->redirectToRoute('app_montre_perso_creer');
            }

            if (empty($boitier)) {
                $this->addFlash('error', 'Le boîtier est obligatoire');
                return $this->redirectToRoute('app_montre_perso_creer');
            }

            if (empty($bracelet)) {
                $this->addFlash('error', 'Le bracelet est obligatoire');
                return $this->redirectToRoute('app_montre_perso_creer');
            }

            if (empty($mouvement)) {
                $this->addFlash('error', 'Le mouvement est obligatoire');
                return $this->redirectToRoute('app_montre_perso_creer');
            }

            if (empty($prix) || !is_numeric($prix) || $prix <= 0) {
                $this->addFlash('error', 'Le prix doit être un nombre valide');
                return $this->redirectToRoute('app_montre_perso_creer');
            }

            // Créer la montre
            $montre = new MontrePersonnalisee();
            $montre->setNom($nom);
            $montre->setDescription($description);
            $montre->setBoitier($boitier);
            $montre->setBracelet($bracelet);
            $montre->setMouvement($mouvement);
            $montre->setPrix($prix);
            $montre->setSource($source);
            $montre->setCreateur($this->getUser());

            // Gestion de l'image
            /** @var UploadedFile $file */
            $file = $request->files->get('image');
            if ($file && $file->isValid()) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/montres_perso', $filename);
                $montre->setImage($filename);
            }

            $em->persist($montre);
            $em->flush();

            $this->addFlash('success', 'Votre montre personnalisée a été créée avec succès !');
            return $this->redirectToRoute('app_montre_perso_mes_creations');
        }

        return $this->render('montre_perso/creer.html.twig');
    }

    #[Route('/mes-creations', name: 'app_montre_perso_mes_creations')]
    #[IsGranted('ROLE_USER')]
    public function mesCreations(MontrePersonnaliseeRepository $repository): Response
    {
        $user = $this->getUser();
        $montres = $repository->findByCreateur($user->getId());
        
        return $this->render('montre_perso/mes_creations.html.twig', [
            'montres' => $montres
        ]);
    }

    #[Route('/{id}', name: 'app_montre_perso_detail')]
    public function detail(int $id, MontrePersonnaliseeRepository $repository, EntityManagerInterface $em): Response
    {
        $montre = $repository->find($id);
        
        if (!$montre) {
            throw $this->createNotFoundException('Montre non trouvée');
        }

        $montre->incrementVues();
        $em->flush();

        return $this->render('montre_perso/detail.html.twig', [
            'montre' => $montre
        ]);
    }

    #[Route('/publier/{id}', name: 'app_montre_perso_publier')]
    #[IsGranted('ROLE_USER')]
    public function publier(int $id, MontrePersonnaliseeRepository $repository, EntityManagerInterface $em): Response
    {
        $montre = $repository->find($id);
        
        if (!$montre || $montre->getCreateur() !== $this->getUser()) {
            throw $this->createNotFoundException('Montre non trouvée');
        }

        $montre->setEstPubliee(true);
        $em->flush();

        $this->addFlash('success', 'Votre montre est maintenant publiée !');
        return $this->redirectToRoute('app_montre_perso_mes_creations');
    }

    #[Route('/supprimer/{id}', name: 'app_montre_perso_supprimer')]
    #[IsGranted('ROLE_USER')]
    public function supprimer(int $id, MontrePersonnaliseeRepository $repository, EntityManagerInterface $em): Response
    {
        $montre = $repository->find($id);
        
        if (!$montre || $montre->getCreateur() !== $this->getUser()) {
            throw $this->createNotFoundException('Montre non trouvée');
        }

        // Supprimer l'image si elle existe
        if ($montre->getImage()) {
            $path = $this->getParameter('kernel.project_dir') . '/public/uploads/montres_perso/' . $montre->getImage();
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $em->remove($montre);
        $em->flush();

        $this->addFlash('success', 'Montre supprimée avec succès');
        return $this->redirectToRoute('app_montre_perso_mes_creations');
    }
}
