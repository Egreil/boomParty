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


    public function updateSortie() : void
    {
        $sorties=$this->sortieRepository->findAll();

        foreach ($sorties as $sortie) {
            $dateNow = new \DateTime();
            $dateLimite=$sortie->getDateLimite();
            $dateDebut=$sortie->getDateDebut();
        }

    }

    public function afficherSortiesAHistoriser(){
        return $this->sortieRepository->findSortiesAHistoriser();
    }


    public function historiser(){
        $em=$this->entityManager;
        $sortieRepository=$em->getRepository(Sortie::class);
        $etatHistorise=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Historisée']);
        $sortiesAHistoriser=$sortieRepository->findSortiesAHistoriser();

//
//        foreach($sortiesAHistoriser as $sortie){
//            var_dump($sortie->getNom());
//            var_dump($sortie->getEtat()->getLibelle());
//            $sortie->setEtat($etatHistorise);
//            $em->persist($sortie);
//        }
        $em->flush();
//        $sortiesAHistoriser=$sortieRepository->findSortiesAHistoriser();
//        foreach($sortiesAHistoriser as $sortie) {
//
//            var_dump($sortie->getNom());
//            var_dump($sortie->getEtat()->getLibelle());
//        }
    }

    public function actualiserSortiesEnCours()
    {
        $em = $this->entityManager;
        $sortieRepository = $em->getRepository(Sortie::class);
        //Récupération des sorties à passer "Activité en cours"
        $etatEnCours = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']);
        $sortiesCommencees = $sortieRepository->findSortiesCommencees();
        foreach ($sortiesCommencees as $sortie) {
            $sortie->setEtat($etatEnCours);
            $em->persist($sortie);
        }
        $em->flush();

    }

    //Récupération des sorties à passer "Passée"
    public function actualiserSortiesPassees(){
        $em = $this->entityManager;
        $sortieRepository = $em->getRepository(Sortie::class);
        $etatPassee=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Passée']);
        $sortiesPassees=$sortieRepository->findSortiesPassees();
        foreach($sortiesPassees as $sortie){
            $sortie->setEtat($etatPassee);
            $em->persist($sortie);
        }
        $em->flush();
//        $sortiesAHistoriser=$sortieRepository->findSortiesAHistoriser();
//        foreach($sortiesAHistoriser as $sortie) {
//
//            var_dump($sortie->getNom());
//            var_dump($sortie->getEtat()->getLibelle());
//        }
    }



}