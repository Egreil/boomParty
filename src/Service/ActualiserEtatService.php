<?php

namespace App\Service;


use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ActualiserEtatService
{
     private readonly EntityRepository $sortieRepository;
    public function __construct(private EntityManagerInterface $entityManager){
        $this->sortieRepository=$this->entityManager->getRepository(Sortie::class);
    }


    public function afficherSortiesAHistoriser(){
        return $this->sortieRepository->findSortiesAHistoriser();
    }


    public function historiser(){
        $em=$this->entityManager;
        $etatHistorise=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Historisée']);
        $sortiesAHistoriser=$this->sortieRepository->findSortiesAHistoriser();
        foreach($sortiesAHistoriser as $sortie){
            $sortie->setEtat($etatHistorise);
            $em->persist($sortie);
        }
        $em->flush();
    }

}