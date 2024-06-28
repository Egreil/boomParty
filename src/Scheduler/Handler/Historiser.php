<?php

namespace App\Scheduler\Handler;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;


/**
 * Service qui est invoqué par le scheduler pour Historiser les sorties.
 */
#[AsCronTask('*/1 */1 * * *',timezone:"EUROPE/PARIS")]
final class Historiser
{
    public function __construct (private EntityManagerInterface $entityManager) {

    }
    public function __invoke():string {
        $em=$this->entityManager;
        $sortieRepository=$em->getRepository(Sortie::class);
        $etatHistorise=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Historisée']);
        $sortiesAHistoriser=$sortieRepository->findSortiesAHistoriser();

        foreach($sortiesAHistoriser as $sortie){
            var_dump($sortie->getNom());
            var_dump($sortie->getEtat()->getLibelle());
            $sortie->setEtat($etatHistorise);
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