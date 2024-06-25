<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/villes', name: 'villes_')]
class VilleController extends AbstractController
{
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
