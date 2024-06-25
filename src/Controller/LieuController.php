<?php

namespace App\Controller;

use App\LieuService\LieuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/create', name:'create')]
class LieuController extends AbstractController
{

    #[Route('/lieu', name: 'create')]
    public function create( LieuService $lieuService): Response
    {
        $nom ='Paris';
        $rue='Hola';
        $latitude=48.8536;
        $longitude=2.3568;

        $lieu =$lieuService->createLieu($nom, $rue, $latitude,$longitude);
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
            'lieu' => $lieu,
        ]);
    }
}
