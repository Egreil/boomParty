<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/list',name:'list')]
    public function list(ParticipantRepository $participantRepository, Request $request){

        $participants = $participantRepository->findAll($request);
        return $this->render('Participant/list.html.twig',[
                'participants'=>$participants
            ]);


    }

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
                $this->getUser()->getUserIdentifier();

        }
        return $this->render('participant/details.html.twig', [
            'participant' => $participant,
        ]);
    }
    #[Route('/update', name: 'update')]
    public function update(
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        Request $request
    ){
        $participant=$participantRepository->findUserByPseudo();
        $participant=new Participant();
        $participantForm=$this->createForm(ParticipantType::class,$participant);
        $participantForm->handleRequest($request);
        return $this->render('participant/update.html.twig', []);
    }

}
