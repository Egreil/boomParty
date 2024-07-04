<?php

namespace App\Controller;

use App\Form\SortieFilterType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index( SortieRepository $sortieRepository, Request $request): Response
    {

        $filtreForm = $this->createForm(SortieFilterType::class);

        $filtreForm->handleRequest($request);

        $sorties = [];
        if($filtreForm->isSubmitted()){
            // Récupérer les données filtrées
            $data = $filtreForm->getData();
            $sorties = $sortieRepository->findSortiesByFilters($data, $this->getUser());

        }else {
            // Si le formulaire n'est pas soumis, affichez toutes les sorties
            $sorties = $sortieRepository->findSorties($request);
        }


        return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'filtreForm' => $filtreForm->createView(),
            ]

        );
    }

//    #[Route('/login', name: 'login')]
//    public function login(): Response
//    {
//        return $this->render('security/login.html.twig');
//    }
}
