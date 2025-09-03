<?php

namespace App\Controller;

use App\Repository\MontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(MontreRepository $montreRepository): Response
    {
        $montres = $montreRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'montres' => $montres,
        ]);
    }
    #[Route('/boutique/{id}', name: 'app_boutique_detail')]
public function detail(Montre $montre): Response
{
    return $this->render('boutique/detail.html.twig', [
        'montre' => $montre,
    ]);
}

}
