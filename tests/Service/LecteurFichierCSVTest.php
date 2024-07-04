<?php

namespace App\Tests\Service;

use App\Service\LecteurFichierCSV;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use function PHPUnit\Framework\assertTrue;

class LecteurFichierCSVTest extends KernelTestCase
{
    //Le fichier est lu correctement
    public function testLecteurFichierCSV(){
        $file=fopen(("./tests/DocumentDeTest/Classeur.xlsx"),'r');
        assertTrue($file!=null);
    }

    //Le fichier
    public function testRecuperationDonneesCSV(){
        self::bootKernel();
        $em=static::getContainer()->get(EntityManagerInterface::class);
        $mailer=static::getContainer()->get(MailerInterface::class);
        $userPasswordHasher=static::getContainer()->get(UserPasswordHasherInterface::class);
        $filepath="./tests/DocumentDeTest/Classeur.csv";
        $lecteurFichierCSV= new LecteurFichierCSV();
        $lecteurFichierCSV->lireFichierCSV($em,$filepath,$mailer,$userPasswordHasher);
        assertTrue($filepath!=null);
    }

    public function testEnvoyerMail(){
        self::bootKernel();
        $em=static::getContainer()->get(EntityManagerInterface::class);
        $mailer=static::getContainer()->get(MailerInterface::class);
        $userPasswordHasher=static::getContainer()->get(UserPasswordHasherInterface::class);
        $filepath="./tests/DocumentDeTest/Classeur.csv";
        $lecteurFichierCSV= new LecteurFichierCSV();
        $lecteurFichierCSV->lireFichierCSV($em,$filepath,$mailer,$userPasswordHasher);
        assertTrue($filepath!=null);


    }



}
