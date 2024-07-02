<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\SauvegardeImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/details', name: 'details_personnel')]
    #[Route('/details/{id}', name: 'details',requirements: ['id' => '\d+'])]
    public function afficher(
        ParticipantRepository $participantRepository,
        int $id=null
    ): Response
    {
        if($id){
            $participant=$participantRepository->find($id);
        }
        else{
            $participant=
                $this->getUser();
                //dd($participant);
        }
        return $this->render('participant/details.html.twig', [
            'participant' => $participant,
        ]);
    }
    #[Route('/update', name: 'update')]
    public function update(
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        Request $request,
        SauvegardeImageService $fileUploader
    ){

        $participant=$this->getUser();
        if(!$participant){
            $participant=$participantRepository->findAll()[1];
        }
        $participantForm=$this->createForm(ParticipantType::class,$participant);
        $participantForm->handleRequest($request);





        if($participantForm->isSubmitted() && $participantForm->isValid()){

            $file = $participantForm->get('image')->getData();
            $newFilename = $fileUploader->RenomerImage($file, $this->getParameter('profil_image_directory'), $participant->getPseudo());
            //setté le nouveau nom dans l'objet
            $participant->setImage($newFilename);
            //TODO empêcher la modification d'attributs inaccessibles
            $entityManager ->persist($participant);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('participant/update.html.twig', [
            'participant' => $participant,
            'form' => $participantForm
        ]);
    }

}
