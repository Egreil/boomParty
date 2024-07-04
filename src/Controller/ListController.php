<?php

namespace App\Controller;

use App\Form\SortieFilterType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListController extends AbstractController
{
    #[Route('/list', name: 'list_home')]
    public function home(
        Request $request,
        SortieRepository $sortieRepository,
        CampusRepository $campusRepository,
    ): Response
    {

        $form = $this->createForm(SortieFilterType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $sortie = $sortieRepository->findSorties($data['campus'], $data['motsClés'], $data['dateDebut'], $data['dateFin'], $data['organisateur'], $data['inscrit'], $data['nonInscrit'], $data['sortiesPassees'], $this->getUser());
        } else {
            $sortie = $sortieRepository->findAll();
        }
        $campus = $campusRepository->findAll();
        return $this->render('list/home.html.twig', [
           'sorties' => $sortie,
            'form' => $form->createView(),
            'campus' => $campus,
        ]);
    }
}
