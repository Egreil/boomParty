<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InscriptionMailingService
{

    public function creationCompteVierge(EntityManagerInterface $em,
                                         array $datas=null,
                                         MailerInterface $mailer=null,
                                         Participant $participant=null,
                    UserPasswordHasherInterface $userPasswordHasher=null
    ){
        //Creation du participant à partir des datas si les informations ne proviennent pas d'un formulaire
        if(!$participant && $datas){
            $campus=$em->getRepository(Campus::class)->findOneBy(['nom'=> $datas['Campus']]);
            $participant= new Participant();
            $participant->setNom($datas['Nom'])
                ->setPrenom($datas['Prenom'])
                ->setEmail($datas['Mail'])
                ->setCampus($campus)
                ->setTelephone($datas['Telephone']);
        }// Ajout des infomartions complémentaires et hashage du mot de passe
        if($userPasswordHasher){
            $this->initialiserParticipant($participant,$userPasswordHasher);
        }
        // Insertion du nouveau participant dans la BDD et appel de l'envoie du mail
       if($mailer){
           $em->persist($participant);
           $em->flush();
           $this->envoyerMail($mailer,$participant);
       }
    }

    public function envoyerMail(MailerInterface $mailer,Participant $participant){
            $email = (new TemplatedEmail())
                ->from('boomParty@eni.com')
                ->to($participant->getEmail())
                ->subject("Rejoins ton BDE préféré!")
                ->htmlTemplate('mailer/invitation.html.twig')
                ->context([
                    'participant' => $participant
                ]);

            $mailer->send($email);

    }
    public function initialiserParticipant(Participant $participant,
                                           UserPasswordHasherInterface $userPasswordHasher){
        $participant->setPseudo($participant->getPrenom()[0].$participant->getNom())
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime())
            ->setActif(true)
            ->setRoles(['ROLE_USER'])
            ->setPassword(
                $userPasswordHasher->hashPassword($participant, 'password')
            );
    }
}