<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class InscriptionMailingService
{

    public function creationCompteVierge(EntityManagerInterface $em, array $datas,MailerInterface $mailer=null){
        var_dump($datas['Campus']);
        $campus=$em->getRepository(Campus::class)->findOneBy(['nom'=> $datas['Campus']]);
        $participant= new Participant();
        $participant->setNom($datas['Nom'])
            ->setPrenom($datas['Prenom'])
            ->setEmail($datas['Mail'])
            ->setCampus($campus)
            ->setTelephone($datas['Telephone'])
            ->setPseudo($datas['Prenom'][0].$datas['Nom'])
            ->setDateCreation(new \DateTime())
            ->setDateModification(new \DateTime())
            ->setActif(true)
            ->setRoles(['ROLE_USER'])
            ->setPassword('password');
       if($mailer){
            $this->envoyerMail($mailer,$participant);

        $em->persist($participant);
        $em->flush();
       };
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
}