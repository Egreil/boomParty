<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreerProfilParticipantType;
use App\Form\CsvType;
use App\Repository\ParticipantRepository;
use App\Service\InscriptionMailingService;
use App\Service\LecteurFichierCSV;
use App\Service\SauvegardeImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/creerCompteCSV', name: 'creerCompteCSV')]
    public function creerCompteCSV(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        LecteurFichierCSV $lecteurFichierCSV,
        SauvegardeImageService $fileUploader,
        UserPasswordHasherInterface $userPasswordHasher,
        Request $request

    ): Response
    {
        $participant=new Participant();
        $csvForm=$this->createForm(CsvType::class);
        $csvForm->handleRequest($request);

        if($csvForm->isSubmitted() && $csvForm->isValid()){
            $file = $csvForm->get('csv')->getData();
            $newFilename = $fileUploader->RenomerImage($file, $this->getParameter('csv_directory'),'csv');
            $lecteurFichierCSV->lireFichierCSV($em,$this->getParameter('csv_directory').'/'.$newFilename,$mailer,$userPasswordHasher);
            $this->addFlash('success', 'Le fichier csv a été lu avec succès.');
        }

        return $this->render('admin/inscriptionCSV.html.twig', [
            'csvForm'=>$csvForm
        ]);

    }
    #[Route('/creerCompte', name: 'creerCompte')]
    public function creerCompte(
        InscriptionMailingService $inscriptionMailingService,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        UserPasswordHasherInterface $userPasswordHasher,
        Request $request

    ): Response
    {
        $participant=new Participant();
        $participantForm=$this->createForm(CreerProfilParticipantType::class,$participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() /*&& $participantForm->isValid()*/){
            $inscriptionMailingService->creationCompteVierge($em,null,$mailer,$participant,$userPasswordHasher);
            $this->addFlash('success', 'Le fichier csv a été lu avec succès.');
            return $this->render('admin/inscriptionFormulaire.html.twig', [
                'participantForm'=>$participantForm,
            ]);
        }

        return $this->render('admin/inscriptionFormulaire.html.twig', [
            'participantForm'=>$participantForm,
        ]);

    }
    #[Route('/participants', name: 'participants_')]
    public function afficher(
        ParticipantRepository $participantRepository,
    ): Response
    {
        return $this->render('participant/list.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }
    #[Route('/participant/delete/{id}', name: 'participants_delete',requirements:['id' => '\d+'] )]
    public function delete(
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em,
        int $id=null
    ): Response
    {
        if($id){
            $participant=$participantRepository->find($id);
            if($participant){
                $em->remove($participant);
                $em->flush();
            }

        }
        return $this->render('participant/list.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }
    #[Route('/descativer/{id}', name: 'participants_desactiver',requirements:['id' => '\d+'])]
    public function descativer(
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em,
        int $id=null
    ): Response
    {
        if($id){
            $participant=$participantRepository->find($id);
            $participant->setActif(false);
            $em->persist($participant);
            $em->flush();
        }
        return $this->render('participant/list.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }
}
