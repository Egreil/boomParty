<?php

namespace App\Scheduler\Handler;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Service\ActualiserEtatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('*/1 */1 * * *',timezone:"EUROPE/PARIS")]
class ActualiserEtat
{
    public function __construct (private EntityManagerInterface $entityManager) {

    }


    public function __invoke() {
        $actualiserEtatService=new ActualiserEtatService($this->entityManager);
        //actualisation des sorties à passer "Activité en cours"
        $actualiserEtatService->actualiserSortiesEnCours();
        //actualisation des sorties à passer "Passée"
        $actualiserEtatService->actualiserSortiesPassees();

    }
}