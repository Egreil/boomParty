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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/details', name: 'details_personnel')]
    #[Route('/details/{id}', name: 'details',requirements: ['id' => '\d+'])]
    public function afficher(
        ParticipantRepository $participantRepository,
        int $id=null,
        Request $request,
        EntityManagerInterface $entityManager,
        SauvegardeImageService $fileUploader,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        //$participant=$this->getUser();

        if($id){
            $participant=$participantRepository->find($id);
        }
        else{
            $participant= $this->getUser();
        }

        $participantForm = $this->createForm(ParticipantType::class,$participant);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){

            $password = $participantForm->get('password')->getData();
            if($password !== null){
                $participant->setPassword(
                    $userPasswordHasher->hashPassword(
                        $participant,
                        $participantForm->get('password')->getData()
                    )
                );
            }

            $file = $participantForm->get('image')->getData();
            //dd($file);
            if($file !== null){
                $newFilename = $fileUploader->RenomerImage($file, $this->getParameter('profil_image_directory'), $participant->getPseudo());
                $participant->setImage($newFilename);
            }

            $participant->setDateModification(new \DateTime());
            $entityManager ->persist($participant);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_list');
        }



        return $this->render('participant/details.html.twig', [
            'participant' => $participant,
            'participantForm' => $participantForm->createView(),
        ]);
    }

}
