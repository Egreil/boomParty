<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


/**
 * Service qui est invoqué par le scheduler pour Historiser les sorties.
 */
use Symfony\Component\Scheduler\Attribute\AsCronTask;


#[AsCronTask('*/1 */1 * * *')]
readonly final class Historiser
{
    public function __construct (private EntityManagerInterface $entityManager) {

    }
    public function __invoke():string {

        $em=$this->entityManager;
        $sortieRepository=$em->getRepository(Sortie::class);
        $etatHistorise=$em->getRepository(Etat::class)->findOneBy(['libelle'=>'Historisée']);
        $sortiesAHistoriser=$sortieRepository->findSortiesAHistoriser();
        var_dump($sortiesAHistoriser);
        foreach($sortiesAHistoriser as $sortie){
            $sortie->setEtat($etatHistorise);
            $em->persist($sortie);
        }
        $em->flush();
        return "ok";
    }
}