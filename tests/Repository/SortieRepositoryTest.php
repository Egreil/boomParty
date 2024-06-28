<?php

namespace App\Tests\Repository;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use App\Service\ActualiserEtatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SortieRepositoryTest extends KernelTestCase
{

    public function testFindSortiesAHistoriser(){
        $sortie = new Sortie();
// VERSION MOCK
//        $sortieRepository=$this->createMock(SortieRepository::class);
//        $sortieRepository->expects($this->any())
//            ->method('find')
//            ->willReturn($sortie);
//        $entityManager = $this->createMock(EntityManagerInterface::class);
//        $entityManager->expects($this->any())
//            ->method('getRepository')
//            ->willReturn($sortieRepository);
//        $actualiserEtatService=new ActualiserEtatService($sortieRepository,$entityManager);

        //Version avec une bbd
        //recupération du sortie Repository a partir du kernel
        self::bootKernel();
        $sortieRepository=static::getContainer()->get(SortieRepository::class);
        //Fonction à tester
        $sorties=$sortieRepository->findSortiesAHistoriser();
        //var_dump($sorties);
        var_dump(count([$sorties]));
        $bool=function($sorties){
            foreach($sorties as $sortie){
                var_dump($sortie->getNom());
                var_dump($sortie->getEtat()->getLibelle());
                if(($sortie->getEtat()->getLibelle()!="Passée") && ($sortie->getEtat()->getLibelle()!="Annulée")){

                    return false;
                }
            }
            return true;
        };
        $this->assertTrue($bool($sorties));
    }

//    public function testHistorisation(){
//        //Arrange
//
//        //Act
//        $sortie=$this->entityManager->getRepository(SortieRepository::class)->findSortiesAHistoriser();
//        var_dump($sortie);
//        //assert
//        $this->assertTrue($sortie->count()>=1);
//    }
}
