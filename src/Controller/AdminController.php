<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreerProfilParticipantType;
use App\Form\CsvType;
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
}
