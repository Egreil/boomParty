<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/update/{id}', name:'update',requirements: ['id'=> '\d+'])]
    #[Route('/create', name:'create')]
    public function createSortie(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id=null,
        SortieRepository $sortieRepository
    ): Response
    {

        if($id){
            $sortie=$sortieRepository->find($id);
        }
        else{
            $sortie=new Sortie();
        }
        $sortieForm=$this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_details',[
                'id'=>$sortie->getId()
            ]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortie' => $sortie,
            'form' => $sortieForm,
        ]);
    }
    #[Route('/list',name:'list')]
    #[Route('/',name:'')]
    public function listSorties(
        SortieRepository $sortieRepository,
        Request $request,
    ){
        $sorties=$sortieRepository->findSorties($request);

        return $this->render('sortie/list.html.twig',[
            'sorties'=>$sorties
        ]

        );
    }


    #[Route('/delete/{id}', name:'delete',requirements: ['id'=> '\d+'])]
    public function deleteSortie(
        EntityManagerInterface $entityManager,
        int $id=null,
        SortieRepository $sortieRepository
    ): Response
    {
        if($id){
            $sortie=$sortieRepository->find($id);
        }
        $entityManager->remove($sortie);
        $entityManager->flush();
        return $this->render('sortie/list.html.twig', [
        ]);
    }
    #[Route('/details/{id}', name:'details',requirements: ['id'=> '\d+'])]
    public function detailSortie(
    SortieRepository $sortieRepository,
    int $id=null,
    ){
        if(!$id) {
            throw $this->createNotFoundException('Identifiants introuvable');
        }
        $sortie = $sortieRepository->find($id);

        if(!$sortie) {
            throw $this->createNotFoundException('Sortie inexistante');
        }

        $sortiesWithPostalCode =$sortieRepository->findSortiesByCityAndPlace();
 return $this->render('sortie/details.html.twig',[
            'sortie'=>$sortie,
            'sortiesWithPostalCode'=>$sortiesWithPostalCode,
        ]);
    }
}
