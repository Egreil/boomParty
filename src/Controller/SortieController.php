<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieFilterType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Service\InscriptionSortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    public function __construct(private readonly InscriptionSortieService $inscriptionSortieService)
    {
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    #[Route('/create', name: 'create')]
    public function createSortie(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id = null,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        LieuRepository $lieuRepository
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
        // mon formulaire Lieu
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            // normalement ici je dois recuperer les coordonnes du lieu
//                $locations = $geoApiService->getLocationsByCity($lieu->getNom());
//                $lieu->setRue($locations['rue']);
//                $lieu->setLatitude($locations['latitude']);
//                $lieu->setLongitude($locations['longitude']);

            $entityManager->persist($lieu);
            $entityManager->flush();


            $this->addFlash('success', 'Le lieu ' . $lieu->getNom() . ' a été créé avec succès.');
            return $this->redirectToRoute('sortie_create', ['lieu' => $lieu->getId()]);
        }
        // Si je choisi un lieu depuis ma liste déroulante
        if ($request->query->has('lieuId')) {
            $lieuId = $request->query->getInt('lieuId');
            $lieu = $lieuRepository->find($lieuId);

            if ($lieu) {
                // Remplissage des coordonnées du lieu dans la sortie
                $sortie->setLieu($lieu);
                $sortie->setLieuRue($lieu->getRue());
                $sortie->setLieuLatitude($lieu->getLatitude());
                $sortie->setLieuLongitude($lieu->getLongitude());
                $sortie->setLieuCodePostal($lieu->getVille()->getCodePostal());

            }
        }


        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $user = $this->getUser();
            $sortie->setOrganisateur($user);

            $action = $request->request->get('action');

            if ($action === 'enregistrer') {
                // Enregistrer la sortie sans la publier
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Crée']));
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a été enregistrée avec succès.');

            } elseif ($action === 'publier') {
                // Publier la sortie si je click sur le bouton publier
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
                $sortie->setDateHeureDebut(new \DateTime());
                $sortie->setDateLimiteInscription(new \DateTime());

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie ' . $sortie->getNom() . ' a été publiée avec succès.');
            } elseif ($action === 'supprimer') {
                return $this->redirectToRoute('sortie_delete', ['id' => $sortie->getId()]);

            } elseif ($action === 'annuler') {
                return $this->redirectToRoute('sortie_annuler', ['id' => $sortie->getId()]);
            }

            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        $template = $id ? 'sortie/update.html.twig' : 'sortie/create.html.twig';
        return $this->render($template, [
            'sortie' => $sortie,
            'form' => $sortieForm->createView(),
            'formLieu' => $lieuForm->createView(),
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
            $sorties = $sortieRepository->findSortiesByFilters($data, $this->getUser());

        }else {
            // Si le formulaire n'est pas soumis, affichez toutes les sorties
            $sorties = $sortieRepository->findSorties();
        }


        return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'filtreForm' => $filtreForm->createView(),
            ]

        );
    }


    #[Route('/annuler/{id}', name: 'annuler_sortie', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function annulerSortie(
        SortieRepository $sortieRepository,
        int $id
    ) : Response
    {
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            return $this->json(['error' => 'Sortie introuvable'], Response::HTTP_NOT_FOUND);
        }
        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/annuler/{id}', name: 'annuler', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteSortie(
        EntityManagerInterface $entityManager,
        int $id = null,
        Request $request,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            $this->addFlash('error', 'Sortie introuvable');
            return $this->redirectToRoute('sortie_list');
        }

        $motif=$request->request->get('motif');

        if(!$motif) {
            $this->addFlash('error', 'Le motif doit etre renseigner');
            return $this->redirectToRoute('sortie_list');
        }
        $sortie->setInfosSortie($motif);

        $etatSortieAnnulee = $etatRepository->findOneBy(['libelle' => 'Annulée']);
        if(!$etatSortieAnnulee){
            $this->addFlash('error', 'Etat "Annulée" non trouvée !');
            return $this->redirectToRoute('sortie_list');
        }

        $sortie->setEtat($etatSortieAnnulee);

        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash('success', 'La sortie est annulée');
        return $this->redirectToRoute('sortie_list');

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

    #[Route('/inscrire/{id}', name: 'inscrire', requirements: ['id' => '\d+'])]
    public function inscrireSortie(
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        Security $security,
        int $id
    ): Response {
        // la je récupére la sortie par son id
        $sortie = $sortieRepository->find($id);

        // ici je verifie si la sortie existe
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas !');
        }

        // je récupére l'utilisateur actuel
        $participant = $security->getUser();

        // je verifie si l'utilisateur est connecté
        if (!$participant instanceof Participant) {
            $this->addFlash('error', 'Vous devez être connecté(e) pour vous inscrire à une sortie.');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        // je verifie la si l'utilisateur est déjà inscrit
        if ($sortie->getParticipants()->contains($participant)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        // je verifie aussi si le nombre maximum de participants est atteint
        if ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionMax()) {
            $this->addFlash('error', 'Le nombre maximum d\'inscriptions est atteint pour cette sortie.');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }
        //dd(date_diff(new \DateTime(),$sortie->getDateLimiteInscription())->days);
        //je vérifie que la date limite n'est pas atteinte
        if ((date_diff(new \DateTime(),$sortie->getDateLimiteInscription()))->days<0) {
            $this->addFlash('error', 'Le date limite d\'inscriptions est passée pour cette sortie.');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        // j'ajouter l'utilisateur à la sortie
        $sortie->addParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();


        $this->addFlash('success', 'Vous êtes inscrit avec succès à la sortie ' . $sortie->getNom());

        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }

    #[Route('/deinscrire/{id}', name: 'deinscrire', requirements: ['id' => '\d+'])]
    public function deinscrireSortie(
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        Security $security,
        int $id = null
    ) : Response {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
        }

        $participant = $security->getUser();
        if (!$participant instanceof Participant) {
            $this->addFlash('error', 'Vous devez être connecté(e) pour vous désinscrire d\'une sortie.');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }
        //je vérifie que la date limite n'est pas atteinte

        if (new \DateTime()>$sortie->getDateHeureDebut()) {
            $this->addFlash('error', 'L\'activitée est commencée, vous ne pouvez plus vous désinscrire');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        if (!$sortie->getParticipants()->contains($participant)) {
            $this->addFlash('error', 'Vous n\'êtes pas inscrit à cette sortie.');
        } else {
            $sortie->removeParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez été désinscrit avec succès de la sortie ' . $sortie->getNom());
        }

        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }


    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public  function delete(
        int $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
    ) : Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
            return $this->redirectToRoute('sortie_list');
        }
        $entityManager->remove($sortie);
        $entityManager->flush();

        $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' est supprimée par : ' . $sortie->getOrganisateur()->getNom() . ' !');


        return $this->redirectToRoute('sortie_list');
    }
}
