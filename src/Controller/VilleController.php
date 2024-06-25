<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/villes', name: 'villes_')]
class VilleController extends AbstractController
{

    #[Route('/update/{id}', name:'update',requirements: ['id'=> '\d+'])]
    #[Route('/create', name:'create')]
    public function createVilles(
        Request $request,
        VilleRepository $villeRepository,
        EntityManagerInterface $entityManager,
        int $id=null
    ): Response
    {

        return $this->render('villes/create.html.twig');



    }

    #[Route('/list', name: 'list')]
    public function list(
        VilleRepository $repo,
        Request $request
    ): Response {
        $codePostal = $request->query->get('codePostal');

        if (!$codePostal) {
            throw $this->createNotFoundException('Code postal manquant !');
        }

        $villes = $repo->findBy(['codePostal' => $codePostal]);

        return $this->render('villes/list.html.twig', [
            'villes' => $villes,
        ]);
    }
}
