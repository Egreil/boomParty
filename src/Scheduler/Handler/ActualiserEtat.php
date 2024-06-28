<?php

namespace App\Scheduler\Handler;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('*/1 */1 * * *',timezone:"EUROPE/PARIS")]
class ActualiserEtat
{
    public function __construct (private EntityManagerInterface $entityManager) {

    }


    public function __invoke():string {
        $em=$this->entityManager;
        $sortieRepository=$em->getRepository(Sortie::class);
        //Récupération des sorties à passer "Activité en cours"
        $etatEnCours=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Activité en cours']);
        $sortiesCommencees=$sortieRepository->findSortiesCommencees();
        foreach($sortiesCommencees as $sortie){
            $sortie->setEtat($etatEnCours);
            $em->persist($sortie);
        }


        //Récupération des sorties à passer "Passée"
        $etatPassee=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Passée']);
        $sortiesPassees=$sortieRepository->findSortiesPassees();
        foreach($sortiesPassees as $sortie){
            $sortie->setEtat($etatPassee);
            $em->persist($sortie);
        }
        $em->flush();
        $sortiesAHistoriser=$sortieRepository->findSortiesAHistoriser();
        foreach($sortiesAHistoriser as $sortie) {

            var_dump($sortie->getNom());
            var_dump($sortie->getEtat()->getLibelle());
        }

        return "ok";
    }
}