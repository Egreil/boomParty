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
        //var_dump($datas['Campus']);

        if(!$participant && $datas){
            $campus=$em->getRepository(Campus::class)->findOneBy(['nom'=> $datas['Campus']]);
            $participant= new Participant();
            $participant->setNom($datas['Nom'])
                ->setPrenom($datas['Prenom'])
                ->setEmail($datas['Mail'])
                ->setCampus($campus)
                ->setTelephone($datas['Telephone']);
        }
        if($userPasswordHasher){
            $this->initialiserParticipant($participant,$userPasswordHasher);
        }

        var_dump($participant);
       if($mailer){

           $this->envoyerMail($mailer,$participant);
           $em->persist($participant);
           $em->flush();

       }
        return $participant;
    }

    public function envoyerMail(MailerInterface $mailer,Participant $participant){
            $email = (new TemplatedEmail())
                ->from('boomParty@eni.com')
                ->to($participant->getEmail())
                ->subject("Rejoins ton BDE préféré!")
                ->htmlTemplate('mailer/invitation.html.twig')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'username' => 'foo',
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