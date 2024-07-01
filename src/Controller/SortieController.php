<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFilterType;
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
    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    #[Route('/create', name: 'create')]
    public function createSortie(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id = null,
        SortieRepository $sortieRepository
    ): Response {
        if ($id) {
            $sortie = $sortieRepository->find($id);

            if (!$sortie) {
                throw $this->createNotFoundException('La sortie n\'existe pas !');
            }
            $isUpdate = true;
        } else {
            $sortie = new Sortie();
            $isUpdate = false;
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $user = $this->getUser();
            $sortie->setOrganisateur($user);

            $published = false;

            if ($sortieForm->has('publier') && $sortieForm->get('publier')->isClicked()) {
                $published = true;
            }

            if (!$isUpdate) {
                $sortie->setDateHeureDebut(new \DateTime());
                $sortie->setDateLimiteInscription(new \DateTime());
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            if ($published) {
                $this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a été publiée avec succès.');
            } else {
                if ($isUpdate) {
                    $this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a été modifiée avec succès.');
                } else {
                    $this->addFlash('info', 'La sortie ' . $sortie->getNom() . ' a été enregistrée avec succès.');
                }
            }

            return $this->redirectToRoute('sortie_details', [
                'id' => $sortie->getId()
            ]);
        }

        $template = $id ? 'sortie/update.html.twig' : 'sortie/create.html.twig';
        return $this->render($template, [
            'sortie' => $sortie,
            'form' => $sortieForm->createView(),
        ]);
    }

    #[Route('/list', name: 'list')]
    #[Route('/', name: '')]
    public function listSorties(
        SortieRepository $sortieRepository,
        Request          $request,
    )
    {

        $filtreForm = $this->createForm(SortieFilterType::class);

        $filtreForm->handleRequest($request);

        $sorties = [];
        if($filtreForm->isSubmitted()){
            // Récupérer les données filtrées
            $data = $filtreForm->getData();
            $dateDebut = $data['dateDebut'] ?? null;
            $dateFin = $data['dateFin'] ?? null;
            $organisateur = $data['organisateur'] ?? false;
            $inscrit = $data['inscrit'] ?? false;
            $nonInscrit = $data['nonInscrit'] ?? false;
            $sortiePasse = $data['sortiePasse'] ?? false;
            $campus = $data['Campus'] ?? null;
            $nom = $data['nom'] ?? '';

            dd($data);

            $sorties = $sortieRepository->findSortiesByFilters($dateDebut, $dateFin, $organisateur, $inscrit, $nonInscrit, $sortiePasse, $campus, $nom, $this->getUser());

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


    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function deleteSortie(
        EntityManagerInterface $entityManager,
        int $id = null,
        SortieRepository $sortieRepository
    ): Response {
        try {
            if (!$id) {
                throw $this->createNotFoundException('Identifiant de sortie manquant.');
            }


            $sortie = $sortieRepository->find($id);

            if (!$sortie) {
                throw $this->createNotFoundException('Sortie non trouvée pour l\'identifiant ' . $id);
            }


            $entityManager->remove($sortie);
            $entityManager->flush();


            $this->addFlash('success', 'La sortie a été supprimée avec succès.');

            return $this->redirectToRoute('sortie_create');
        } catch (\Exception $e) {

            $this->addFlash('error', 'Une erreur s\'est produite lors de la suppression de la sortie.');

            return $this->redirectToRoute('sortie_create');
        }
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
            throw $this->createNotFoundException('Cet événement n\'existe pas :(' );
        }

        $sortiesWithPostalCode =$sortieRepository->findSortiesByCityAndPlace();
//
//        dd($sortie);
        return $this->render('sortie/details.html.twig',[
            'sortie'=>$sortie,
            'sortiesWithPostalCode'=>$sortiesWithPostalCode,
        ]);
    }
}
