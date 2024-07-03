<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LecteurFichierCSV
{
    public function lireFichierCSV(EntityManagerInterface $em,
                                   string $filepath,
                                   MailerInterface $mailer=null,
                                   UserPasswordHasherInterface $userPasswordHasher
    ){
        $inscriptionMailingService=new InscriptionMailingService();
        //Ourvrir le fichier
        if(($stream=fopen($filepath,'r'))!=FALSE){
            if(($datas=fgetcsv($stream))!=false){
                $titre=explode(";",$datas[0]);
            }
            //Lire la ligne
            while (($datas=fgetcsv($stream))!=false){
                $data=$this->associerDonneeValeur($datas,$titre);
                var_dump($data);
                $participant=$inscriptionMailingService->creationCompteVierge($em,$data,$mailer,null,$userPasswordHasher);
            }


        }
    }

    public function associerDonneeValeur(array $datas,array $titres){
        $datas=explode(";",$datas[0]);
        for ($i=0;$i<count($datas);$i++){
            $tab[$titres[$i]]=$datas[$i];
        }
        //var_dump($tab);
        return $tab;
    }
}